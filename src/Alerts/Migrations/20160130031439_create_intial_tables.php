<?php

use Phinx\Migration\AbstractMigration;

class CreateIntialTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $users = $this->table('users');
        $users->addColumn('email', 'string', ['length' => 255])
            ->addColumn('githubId', 'biginteger', ['signed' => false])
            ->addColumn('githubAccessToken', 'string', ['length' => 255])
            ->addTimestamps()
            ->addIndex('email', ['unique' => true])
            ->addIndex('githubId', ['unique' => true])
            ->create();
        //Phinx is dumb and doesn't make Auto Incrementing Primary Keys unsigned when it creates them for you
        $users->changeColumn('id', 'integer', ['signed' => false, 'identity' => true])->update();

        $clients = $this->table('oauth_clients', ['id' => false, 'primary_key' => ['client_id']]);
        $clients->addColumn('client_id', 'string', ['length' => 128, 'null' => false])
            ->addColumn('client_secret', 'string', ['length' => 128, 'null' => false])
            ->addColumn('grant_types', 'string', ['length' => 128])
            ->addColumn('scope', 'string', ['length' => 128])
            ->create();

        $accessTokens = $this->table('oauth_access_tokens', ['id' => false, 'primary_key' => ['access_token']]);
        $accessTokens->addColumn('access_token', 'string', ['length' => 128, 'null' => false])
            ->addColumn('client_id', 'string', ['length' => 128, 'null' => false])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('expires', 'timestamp', ['null' => false])
            ->addColumn('scope', 'string', ['length' => 128])
            ->addForeignKey('client_id', $clients, 'client_id')
            ->addForeignKey('user_id', 'users', 'id')
            ->create();

        $refreshTokens = $this->table('oauth_refresh_tokens', ['id' => false, 'primary_key' => ['refresh_token']]);
        $refreshTokens->addColumn('refresh_token', 'string', ['length' => 128, 'null' => false])
            ->addColumn('client_id', 'string', ['length' => 128, 'null' => false])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('expires', 'timestamp', ['null' => false])
            ->addColumn('scope', 'string', ['length' => 128])
            ->addForeignKey('client_id', $clients, 'client_id')
            ->addForeignKey('user_id', 'users', 'id')
            ->create();

        $scopes = $this->table('oauth_scopes', ['id' => false, 'primary_key' => ['scope']]);
        $scopes->addColumn('scope', 'string', ['length' => 128])
            ->addColumn('is_default', 'boolean')
            ->create();
    }
}
