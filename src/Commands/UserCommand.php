<?php

namespace NickDeKruijk\Admin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use NickDeKruijk\Admin\Helpers;

class UserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update a user with random password.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'admin:user {' . $this->getUsernameColumn() . ' : A valid ' . ($this->getUsernameColumn() == 'email' ? 'e-mail address' :  $this->getUsernameColumn()) . '} {name? : The fullname of the user, if ommited the name part of the e-mail address is used}';

        parent::__construct();
    }

    private function getUsernameColumn()
    {
        return config('admin.credentials')[0];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $password = Str::random(40);
        $user = Helpers::userModel()->where($this->getUsernameColumn(), $this->arguments()[$this->getUsernameColumn()])->first();
        if ($user) {
            // Existing user, update name and password
            $password = $this->ask('New password (leave blank to leave unchanged');
            if ($password) {
                $user->password = Hash::make($password);
            }
            if ($this->argument('name')) {
                $user->name = $this->argument('name');
            }
            $user->save();
            $status = 'updated';
        } else {
            // Create new user
            $user = Helpers::userModel()::create([
                $this->getUsernameColumn() => $this->arguments()[$this->getUsernameColumn()],
                'name' => $this->arguments()['name'] ?: ucfirst(explode('@', $this->arguments()[$this->getUsernameColumn()])[0]),
                'password' => Hash::make($this->ask('Password (blank for ' . $password . ')') ?: Str::random(40)),
            ]);
            $status = 'created';
        }
        $this->info('User ' . $user[$this->getUsernameColumn()] . ' "' . $user->name . '" ' . $status);
    }
}
