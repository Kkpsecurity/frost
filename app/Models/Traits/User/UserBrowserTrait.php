<?php
declare(strict_types=1);

namespace App\Models\Traits\User;

use App\Models\UserBrowser;
use KKP\TextTk;


trait UserBrowserTrait
{


    public function SetBrowser( string $browser = '' ) : void
    {

        if ( ! $browser = TextTk::Sanitize( $browser ) )
        {
            // sanitizer returned empty string
            return;
        }

        if ( $UserBrowser = $this->UserBrowser )
        {

            if ( $UserBrowser->browser != $browser )
            {
                $UserBrowser->update([ 'browser' => $browser ]);
            }

        }
        else
        {

            $this->UserBrowser = UserBrowser::create([
                'user_id' => $this->id,
                'browser' => $browser,
            ]);

        }

    }


}
