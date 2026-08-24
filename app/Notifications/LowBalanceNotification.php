<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    use Queueable;

    protected $project;
    protected $availableBalance;
    protected $expense;

   
    public function __construct($project, $availableBalance, $expense)
    {
        $this->project = $project;
        $this->availableBalance = $availableBalance;
        $this->expense = $expense;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

   
    public function toMail(object $notifiable): MailMessage
    {
        $projectName = $this->project->name;
        $balance = number_format($this->availableBalance, 2);
        $expenseAmount = number_format($this->expense->amount, 2);
        $personName = $this->expense->name_people ?? 'Usuario no identificado';
        $expenseDate = $this->expense->expense_date->format('d/m/Y H:i');
        $totalExpenses = number_format($this->project->expenses()->sum('amount'), 2);

        return (new MailMessage)
            ->from('info@fiestatoursperu.com', 'Aviso de Recarga - ' . $projectName)
            ->subject('Alerta: Balance Crítico en ' . $projectName)
            ->view('mails.low-balance', [
                'projectName' => $projectName,
                'balance' => $balance,
                'expenseAmount' => $expenseAmount,
                'personName' => $personName,
                'expenseDate' => $expenseDate,
                'totalExpenses' => $totalExpenses,
                'projectId' => $this->project->id_proyect,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id_proyect,
            'project_name' => $this->project->name,
            'balance' => $this->availableBalance,
            'expense_amount' => $this->expense->amount,
            'person' => $this->person ? $this->person->name : null,
        ];
    }
}