<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\UserTypeRepository;
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
        private UserTypeRepository $userTypeRepository,
    ) {}

    /**
     * Register new user
     */
    public function store(array $data): ?User
    {
        $data['cpf'] = $this->onlyNumbers($data['cpf']);

        $user = $this->createUser($data);
        $user->createAccessToken();

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
        $user->createAccessToken();

        return $user;
    }

    /**
     * Create User
     */
    public function createUser(array $data): Model
    {
        $userType = $this->userTypeRepository
            ->findBy('role', data_get($data, 'type', 'staff'));

        return $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'city' => $data['city'],
            'state' => $data['state'],
            'password' => Hash::make($data['password']),
            'user_type_id' => $userType->id,
        ]);
    }
}
