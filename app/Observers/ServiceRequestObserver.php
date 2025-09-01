<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Filament\Admin\Resources\ServiceRequestResource;
use App\Filament\Service\Resources\ServiceRequestResource as MechanicServiceRequest;

class ServiceRequestObserver
{
    /**
     * Handle the ServiceRequest "created" event.
     */
    public function created(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->load('user');
        $this->notifyUsers($serviceRequest);
    }

    /**
     * Handle the ServiceRequest "updated" event.
     */
    public function updated(ServiceRequest $serviceRequest): void
    {
        $currentUser = auth()->user();

        // $this->notifyAdminsOfUpdate($serviceRequest, $currentUser);

        if ($currentUser->hasRole('mechanic')) {
            $serviceRequest->load('mechanic');
            $this->notifyAdminsOfUpdate($serviceRequest, $currentUser);
        }
    }

    /**
     * Handle the ServiceRequest "deleted" event.
     */
    public function deleted(ServiceRequest $serviceRequest): void
    {
        //
    }

    /**
     * Handle the ServiceRequest "restored" event.
     */
    public function restored(ServiceRequest $serviceRequest): void
    {
        //
    }

    /**
     * Handle the ServiceRequest "force deleted" event.
     */
    public function forceDeleted(ServiceRequest $serviceRequest): void
    {
        //
    }

    private function createNotification(ServiceRequest $serviceRequest, User $recipient, string $type = 'created'): Notification
    {
        if ($type === 'updated') {
            $title = 'Service Request Updated';
            $body = "Service request #{$serviceRequest->service_number} has been updated by a mechanic.";

            // Add status change info if status was changed
            if ($serviceRequest->wasChanged('status')) {
                $oldStatus = ucwords($serviceRequest->getOriginal('status'));
                $newStatus = ucwords($serviceRequest->status);
                $body .= " Status changed from '{$oldStatus}' to '{$newStatus}'.";
            }
        } else {
            $title = 'New Service Request';
            $body = "A new service request has been created by {$serviceRequest->mechanic->name}.";
        }

         if ($recipient->hasRole('super_admin')) {
            return Notification::make()
                ->title($title)
                ->icon('heroicon-o-wrench-screwdriver')
                ->body($body)
                ->actions([
                    Action::make('view')
                        ->button()
                        ->icon('heroicon-o-eye')
                        ->label('View')
                        ->url(MechanicServiceRequest::getUrl('edit', ['record' => $serviceRequest->id]))
                ]);
        } else {
                return Notification::make()
                    ->title($title)
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->body($body)
                    ->actions([
                        Action::make('view')
                            ->button()
                            ->icon('heroicon-o-eye')
                            ->label('View')
                            ->url(ServiceRequestResource::getUrl('edit', ['record' => $serviceRequest->id]))
                    ]);
        }
    }

    // private function getUrlForUser(ServiceRequest $serviceRequest, User $user): string
    // {
    //     if ($user->hasRole('super_admin')) {
    //         return ServiceRequestResource::getUrl('edit', ['record' => $serviceRequest->id]);
    //     } else {
    //         return MechanicServiceRequest::getUrl('edit', ['record' => $serviceRequest->id]);
    //     }
    // }

    private function notifyUsers(ServiceRequest $serviceRequest): void
    {
        $users = User::role(['super_admin', 'mechanic'])->get(['id', 'name']);
        foreach ($users as $user) {
            $notification = $this->createNotification($serviceRequest, $user, 'created');
            $notification->sendToDatabase($user);
        }
    }

    private function notifyAdminsOfUpdate(ServiceRequest $serviceRequest, User $mechanic): void
    {
        $admins = User::role('super_admin')->get(['id', 'name']);
        foreach ($admins as $admin) {
            $notification = $this->createNotification($serviceRequest, $admin, 'updated');
            $notification->sendToDatabase($admin);
        }
    }
}
