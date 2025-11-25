<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $appointmentId = $request->get('appointment_id');
        $documentType = $request->get('document_type');
        $status = $request->get('status');
        $perPage = $request->get('per_page', 20);

        $query = Document::where('user_id', $user->id);

        if ($appointmentId) {
            $query->where('appointment_id', $appointmentId);
        }

        if ($documentType) {
            $query->where('document_type', $documentType);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $documents = $query->latest()->paginate($perPage);

        AuditLog::log('view', 'Document', null, 'Listed documents');

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'appointment_id' => 'nullable|exists:appointments,id',
            'document_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($request->file('appointment_id')) {
            $appointment = $user->appointments()
                ->where('id', $validated['appointment_id'])
                ->first();
            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized appointment'
                ], 403);
            }
        }

        $file = $request->file('file');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'private');

        $document = Document::create([
            'user_id' => $user->id,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_path' => $path,
            'document_type' => $validated['document_type'],
            'description' => $validated['description'] ?? null,
            'status' => 'uploaded'
        ]);

        // Create first version
        DocumentVersion::create([
            'document_id' => $document->id,
            'filename' => $filename,
            'file_path' => $path,
            'version_number' => 1,
            'change_type' => 'upload',
            'change_description' => 'Initial upload'
        ]);

        AuditLog::log('create', 'Document', $document->id, 'Document uploaded: ' . $file->getClientOriginalName());

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'data' => $document
        ]);
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        // Check authorization
        if ($document->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        AuditLog::log('download', 'Document', $document->id, 'Document downloaded: ' . $document->original_name);

        return Storage::disk('private')->download($document->file_path, $document->original_name);
    }

    public function show($id)
    {
        $document = Document::with('versions')->findOrFail($id);
        $user = Auth::user();

        if ($document->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        AuditLog::log('view', 'Document', $document->id, 'Document viewed: ' . $document->original_name);

        return response()->json([
            'success' => true,
            'data' => $document
        ]);
    }

    public function delete($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        if ($document->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        Storage::disk('private')->delete($document->file_path);

        AuditLog::log('delete', 'Document', $document->id, 'Document deleted: ' . $document->original_name);

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    public function getVersions($id)
    {
        $document = Document::with('versions')->findOrFail($id);
        $user = Auth::user();

        if ($document->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $document->versions()->latest()->get()
        ]);
    }
}
