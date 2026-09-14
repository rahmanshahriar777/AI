<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\BusinessInfo;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || env('APP_ENV') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        // // Grant all abilities to super_admin role
        // Gate::before(function (User $user, string $ability): bool|null {
        //     return $user->hasRole('super_admin') ? true : null;
        // });

        try{
            $businessinfo = BusinessInfo::first();
            View::share('appbrand', $businessinfo);
        }
        catch(Exception){

        }

        try{
            $mail_destination_force = (string)env('MAIL_TO_ADDRESS', '');
            if(!empty($mail_destination_force)){
        
                // Listen for the MessageSending event
                Event::listen(MessageSending::class, function (MessageSending $event) {
                    $mail_destination_force = (string)env('MAIL_TO_ADDRESS', '');
                    // Clear any existing 'to' recipients
                    $event->message->getTo(); // This gets all 'to' recipients
                    $headers = $event->message->getHeaders();
                    $headers->remove('To');

                    // Set the global recipient email and name
                    $globalRecipientEmail = $mail_destination_force; // Use a .env variable here
                    //$globalRecipientName = 'Global Testing Recipient';

                    // Add the new global 'to' recipient
                    $event->message->addTo($globalRecipientEmail);
                });
                
            }
        }
        catch(Exception $e){

        }
        
    }
}
