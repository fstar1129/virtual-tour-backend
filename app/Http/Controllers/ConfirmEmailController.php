<?php

namespace App\Http\Controllers;

use App\User;
use App\Events\UserWasConfirmed;
class ConfirmEmailController extends Controller
{
    /**
     * Validate email confirmation token.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm()
    {
        if ($user = User::confirmEmail(request()->token)) {
            event(new UserWasConfirmed($user));
            return $this->success('Your email has been confirmed.');
        }

        return $this->fail(404);
    }
}
