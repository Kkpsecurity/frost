<?php declare(strict_types=1);

namespace App\Models\Traits\User;

/**
 * @file UserBrowserTrait.php
 * @brief Trait for managing user browser information.
 * @details This trait provides methods to set and retrieve the user's browser information.
 */

use App\Models\UserBrowser;

use App\Helpers\TextTk;


trait UserBrowserTrait
{


    public function SetBrowser(string $browser = ''): void
    {

        if (! $browser = TextTk::Sanitize($browser)) {
            // sanitizer returned empty string
            return;
        }

        if ($UserBrowser = $this->UserBrowser) {

            if ($UserBrowser->browser != $browser) {
                $UserBrowser->update(['browser' => $browser]);
            }
        } else {

            $this->UserBrowser = UserBrowser::create([
                'user_id' => $this->id,
                'browser' => $browser,
            ]);
        }
    }
}
