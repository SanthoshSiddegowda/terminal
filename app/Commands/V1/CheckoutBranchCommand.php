<?php

namespace App\Commands\V1;

use App\Services\V1\CommandService;
use LaravelZero\Framework\Commands\Command;

class CheckoutBranchCommand extends Command
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'checkout {branchName} {stash?}';
	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Checkout to the given branch';
	/**
	 * The CommandService instance.
	 *
	 * @var CommandService
	 */
	protected $commandService;

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
	 * @return true
	 */
	public function handle(): bool
	{
		$isStashNeeded = $this->argument( 'stash' ) ?? false;
		$commands = [];
		if ( !empty( $isStashNeeded ) ) {
			$commands[] = 'git stash';
		}
		$commands[] = "git checkout {$this->argument('branchName')}";
		if ( !empty( $isStashNeeded ) ) {
			$commands[] = 'git stash pop';
		}
		foreach ( $commands as $command ) {
			$this->executeCommand( $command );
		}
		return true;
	}

	/**
	 * Execute a shell command as a task.
	 *
	 * @param string $command
	 * @return void
	 */
	private function executeCommand( string $command ): void
	{
		$this->task( $command, function () use ( $command ) {
			$this->commandService->executeCommand( $command );
		} );
	}
}
