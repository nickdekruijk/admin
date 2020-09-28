<?php

namespace NickDeKruijk\Admin;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:user {email : A valid e-mail address} {role? : The role the (new) user should get}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->description = 'Create or update a user with random password. The role is saved in the "' . config('admin.role_column') . '" column.';
        parent::__construct();
    }

    /**
     * Get the user model instance from admin config
     *
     * @return User;
     */
    public static function userModel()
    {
        $model = config('admin.modules.users.model');
        return new $model;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = self::userModel()->where('email', $this->arguments()['email'])->first();
        $password = Str::random(40);
        echo 'User ' . $this->arguments()['email'] . ' ';
        if ($user) {
            echo 'updated with ';
            if ($this->arguments()['role']) {
                $user[config('admin.role_column')] = $this->arguments()['role'];
                echo 'role "' . $this->arguments()['role'] . '" and ';
            }
        } else {
            echo 'created with ';
            $user = self::userModel();
            $user->email = $this->arguments()['email'];
            $user->name = $this->arguments()['email'];
            $user[config('admin.role_column')] = $this->arguments()['role'] ?: 'admin';
            echo 'role "' . $user[config('admin.role_column')] . '" and ';
        }
        echo 'password "' . $password . '"' . chr(10);
        $user->password = bcrypt($password);
        $user->save();
    }
}
