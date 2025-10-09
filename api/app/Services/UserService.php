<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    /**
     * UserService constructor.
     */
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * Register new user
     *
     * @return User|null
     */
    public function register(array $data): ?User
    {
        $data['cpf'] = $this->onlyNumbers($data['cpf']);

        $user = $this->createUser($data);

        $this->createToken($user);

        return $user;
    }

    /**
     * @throws Exception
     */
    public function login(array $credentials): Model
    {
        if (! Auth::attempt($credentials)) {
            throw new Exception('Invalid user or password');
        }

        $user = Auth::user();
        $this->createToken($user);

        return $user;
    }

    /**
     * Create User
     */
    private function createUser(array $data): Model
    {
        return $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'city' => $data['city'],
            'state' => $data['state'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Create accessToken for user
     */
    private function createToken(User &$user): void
    {
        $user->accessToken = $user->createToken($user->id.'_token')->accessToken;
    }
}
