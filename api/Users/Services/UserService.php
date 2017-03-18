<?php

namespace Api\Users\Services;

use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Api\Users\Exceptions\UserNotFoundException;
use Api\Users\Events\UserWasCreated;
use Api\Users\Events\UserWasDeleted;
use Api\Users\Events\UserWasUpdated;
use Api\Users\Repositories\UserRepository;

class UserService
{
    private $database;

    private $dispatcher;

    private $userRepository;

    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        UserRepository $userRepository
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->userRepository = $userRepository;
    }

    public function getAll($options = [])
    {
        return $this->userRepository->get($options);
    }

    public function getById($userId, array $options = [])
    {
        $user = $this->getRequestedUser($userId);

        return $user;
    }

    public function create($data)
    {
        try {
            $this->database->beginTransaction();

            $user = $this->userRepository->create($data);

            $this->dispatcher->fire(new UserWasCreated($user));
            
            $this->database->commit();
        } catch (Exception $e) {
            $this->database->rollBack();

            throw $e;
        }

        return $user;
    }

    public function update($userId, array $data)
    {
        $user = $this->getRequestedUser($userId);

        try {
            $this->database->beginTransaction();
            
            $this->userRepository->update($user, $data);

            $this->dispatcher->fire(new UserWasUpdated($user));
            
            $this->database->commit();
        } catch (Exception $e) {
            $this->database->rollBack();

            throw $e;
        }

        return $user;
    }

    public function delete($userId)
    {
        $user = $this->getRequestedUser($userId);

        try {
            $this->database->beginTransaction();

            $this->userRepository->delete($userId);

            $this->dispatcher->fire(new UserWasDeleted($user));
    
            $this->database->commit();
        } catch (Exception $e) {
            $this->database->rollBack();

            throw $e;
        }
    }

    private function getRequestedUser($userId)
    {
        $user = $this->userRepository->getById($userId);

        if (is_null($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
