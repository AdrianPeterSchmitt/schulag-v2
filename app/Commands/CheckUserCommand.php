<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckUserCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group       = 'Database';
    protected $name        = 'db:checkuser';
    protected $description = 'Checks the admin user and password hash from the database.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * @return void
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            $user = $db->table('users')->where('email', 'admin@schulag.test')->get()->getRowArray();

            if (!$user) {
                CLI::error("FEHLER: Benutzer 'admin@schulag.test' nicht gefunden.");
                return;
            }

            CLI::write("Benutzer gefunden:");
            CLI::write(json_encode($user, JSON_PRETTY_PRINT));

            $newPassword = 'admin123';
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $db->table('users')->where('id', $user['id'])->update([
                'password' => $newHash,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            CLI::write('');
            CLI::write(CLI::color("Passwort aktualisiert (neu: admin123).", 'green'));

            $updatedUser = $db->table('users')->where('id', $user['id'])->get()->getRowArray();
            if (password_verify($newPassword, $updatedUser['password'])) {
                CLI::write('');
                CLI::write(CLI::color('Neuer Hash verifiziert.', 'green'));
            } else {
                CLI::write('');
                CLI::write(CLI::color('Fehler: Der neue Passwort-Hash konnte nicht verifiziert werden.', 'red'));
            }

        } catch (\Exception $e) {
            CLI::error("Datenbankfehler: " . $e->getMessage());
        }
    }
}
