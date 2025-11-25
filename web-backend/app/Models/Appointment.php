<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_id',
        'type',
        'service_id',
        'service_type',
        'appointment_date',
        'appointment_time',
        'status',
        'purpose',
        'documents',
        'notes',
        'staff_notes'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'documents' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Add this method for appointment types
    public static function getTypes()
    {
        return [
            'consultation' => 'Legal Consultation',
            'document_review' => 'Document Review',
            'contract_drafting' => 'Contract Drafting',
            'court_representation' => 'Court Representation',
            'notary_services' => 'Notary Services',
            'legal_opinion' => 'Legal Opinion',
            'case_evaluation' => 'Case Evaluation',
            'document_notarization' => 'Document Notarization',
            'affidavit' => 'Affidavit',
            'power_of_attorney' => 'Power of Attorney',
            'loan_signing' => 'Loan Signing',
            'real_estate_documents' => 'Real Estate Documents',
            'will_and_testament' => 'Will and Testament',
            'other' => 'Other Legal Services'
        ];
    }
}