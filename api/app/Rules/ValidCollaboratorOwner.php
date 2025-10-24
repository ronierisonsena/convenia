<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCollaboratorOwner implements ValidationRule
{
    public function __construct(
        protected User $collaborator,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $authUser = auth()->user();

        // Own register
        if ($authUser->id === $this->collaborator->id) {
            return;
        }

        // Staff from logged Manager
        if (
            $this->collaborator->staff &&
            $this->collaborator->staff->manager_id === $authUser->id
        ) {
            return;
        }

        throw new AuthorizationException('Forbidden');
    }
}
