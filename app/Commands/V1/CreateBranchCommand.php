<?php

namespace App\Commands\V1;

use App\Services\V1\CommandService;
use LaravelZero\Framework\Commands\Command;

class CreateBranchCommand extends Command
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'make:branch {name} {source?}';
	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Fetch code and make branch out of it';
	/**
	 * @var CommandService
	 */
	private $commandService;

	/**
	 * Constructor.
	 *
	 * @param CommandService $commandService
	 */
	public function __construct( CommandService $commandService )
	{
		parent::__construct();
		$this->commandService = $commandService;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$name = $this->argument( "name" );
		$source = $this->argument( "source" ) ?? "development";
		if ( $this->executeCommand( "git fetch --all" ) ) {
			if ( $this->executeCommand( "git reset --hard" ) ) {
				if ( $this->executeCommand( "git checkout -b $name $source" ) ) {
					$this->executeCommand( "git stash apply" );
				}
			}
		}
	}

	private function executeCommand( string $command ): bool
	{
		return $this->task( $command, function () use ( $command ) {
			$this->commandService->executeCommand( $command );
		} );
	}
}
