<?php

namespace App\Commands\V1;

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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$name = $this->argument( 'name' );
		$source = $this->argument( 'source' ) ?? 'development';
		# Starting Branch Creation Process
		$this->info( '--- Starting Branch Creation Process ---' );
		# Task: Get Latest Codes
		if ( $this->executeTask( 'Get Latest Codes', 'git fetch' ) ) {
			# Task: Stash existing changes
			if ( $this->executeTask( 'Stash existing changes', 'git reset --hard' ) ) {
				# Task: Create New Branch
				if ( $this->executeTask( 'Create New Branch', "git checkout -b $name $source" ) ) {
					# Task: Stash pop
					$this->executeTask( 'Stash pop', 'git stash apply' );
				}
			}
		}
		# Branch Creation Process Completed
		$this->info( '--- Branch Creation Process Completed ---' );
	}

	private function executeTask( $taskName, $command ): bool
	{
		$this->line( '' );
		$this->info( "Executing task: $taskName" );
		exec( $command, $output, $exitCode );
		if ( $exitCode === 0 ) {
			$this->info( "Task: $taskName completed successfully." );
			return true;
		} else {
			$this->error( "Task: $taskName failed." );
			return false;
		}
	}
}
