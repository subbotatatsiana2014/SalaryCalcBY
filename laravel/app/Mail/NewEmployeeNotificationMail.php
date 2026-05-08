<?php

namespace App\Mail;

use App\Models\Employee\Employee;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEmployeeNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Employee $employee;
    public User $user;

    public function __construct(Employee $employee, User $user)
    {
        $this->employee = $employee->load('department');
        $this->user = $user->load('employee.department');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Вы зарегистрированы в системе SalaryCalc BY',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-employee-notification',
            with: [
                'employee' => $this->employee,
                'user' => $this->user,
                'loginUrl' => route('login'),
                'companyName' => config('app.name'),
                'supportEmail' => 'support@salarycalc.by',
                'currentYear' => now()->year,
            ]
        );
    }
}
