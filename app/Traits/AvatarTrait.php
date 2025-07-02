<?php
namespace App\Traits;

/**
 * Avatar Trait
 * @version: 1.0.3
 * @author: Richard Clark
 *
 * @dependences
 * --- Laravolt\Avatar\Avatar
 */

use Auth;
use Laravolt\Avatar\Avatar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Image;

trait AvatarTrait
{
    /**
     * @param $user
     * @param string $size
     * @return mixed
     */
    public function getAvatar(string $size = 'thumb'): mixed
    {
        // Define cache key based on user ID and size
        $cacheKey = 'avatar_' . $this->id . '_' . $size;

        // Retrieve the avatar from the cache if it exists
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($size) {
            if ($this->avatar == "" || $this->use_gravatar === true) {
                return $this->loadDefaultAvatar($this, $size);
            } else {
                $avatar_file = json_decode($this->avatar, true)['filename'];

                $filepath = Storage::disk(config('filesystems.default', 'local'))
                    ->path("avatars/" . $avatar_file);

                if (!file_exists($filepath)) { // Check in public directory
                    return asset("/assets/img/icon/headshot.png");
                } else {
                    return vasset($filepath, true);
                }
            }
        });
    }


    /**
     * @param $user
     * @return string
     */
    public function getGravatarAttribute($user): string
    {
        $hash = md5(strtolower(trim($user->email)));
        return "https://www.gravatar.com/avatar/" . $hash;
    }

    /**
     * @param $user
     * @param $size
     * @return Image|mixed|string
     */
    protected function loadDefaultAvatar($user, string $size): mixed
    {
        $avatar = new Avatar();
        $fullName = $user->fullname();

        if ($user->use_gravatar == true) {
            return $this->getGravatarAttribute($user);
        } else {
            if ($size === 'thumb') {
                return $avatar->create($fullName)->setDimension(100, 100)->toBase64();
            } elseif ($size === 'regular') {
                return $avatar->create($fullName)->setDimension(220, 220)->toBase64();
            } else {
                return false;
            }
        }
    }
}