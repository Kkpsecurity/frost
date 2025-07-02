<?php
//
// add to app\Providers\EventServiceProvider.php
//    protected $subscribe = [ 'App\Listeners\UserEventSubscriber' ];
//

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
// use Illuminate\Auth\Events\Registered;

use App\RCache;
use App\Classes\Redirectors\ExamRedirector;
# use App\Classes\Redirectors\RangeDateRedirector;


class UserEventSubscriber
{


    public function handleUserAuthenticated( Authenticated $event ) : void
    {

        kkpdebug( 'UserEvent', "User Authenticated :: {$event->user}" );

        RCache::StoreUser( $event->user );

        $event->user->InitPrefs();

        ExamRedirector::handle();
        # RangeDateRedirector::handle();

    }


    public function handleUserLogin( Login $event ) : void
    {

        kkpdebug( 'UserEvent', "User Logged In :: {$event->user}" );

        RCache::StoreUser( $event->user );

        $event->user->InitPrefs();

    }


    public function handleUserLogout( Logout $event ) : void
    {

        kkpdebug( 'UserEvent', "User Logged Out :: {$event->user}" );

    }


    /*
    public function handleUserRegistered( Registered $event ) : void
    {
        kkpdebug( 'UserEvent', "User Registered :: {$event->user}" );
    }
    */


    public function subscribe( $events ) : void
    {

        $events->listen(
            'Illuminate\Auth\Events\Authenticated',
            [ UserEventSubscriber::class, 'handleUserAuthenticated' ]
        );

        $events->listen(
            'Illuminate\Auth\Events\Login',
            [ UserEventSubscriber::class, 'handleUserLogin' ]
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            [ UserEventSubscriber::class, 'handleUserLogout' ]
        );

        /*
        $events->listen(
            'Illuminate\Auth\Events\Registered',
            [ UserEventSubscriber::class, 'handleUserRegistered' ]
        );
        */

    }

}
