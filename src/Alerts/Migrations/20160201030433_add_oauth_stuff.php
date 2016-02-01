<?php

use Phinx\Migration\AbstractMigration;

class AddOauthStuff extends AbstractMigration
{
    /**
     * Migrate Up
     *
     * @return void
     */
    public function up()
    {
        $this->execute('INSERT INTO oauth_scopes (scope, is_default) VALUES (\'user\', 1)');
        $this->execute('INSERT INTO oauth_clients (client_id, client_secret, grant_types, scope) VALUES (\'codemana\', \'codemana\', \'refresh_token\', \'user\')');
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $this->execute('DELETE FROM oauth_scopes WHERE scope=\'user\'');
        $this->execute('DELETE FROM oauth_clients WHERE client_id=\'codemana\'');
    }
}
