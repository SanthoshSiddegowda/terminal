<?php

namespace App\Services\V1;

use LaravelZero\Framework\Commands\Command;

class CommandService extends Command
{
	/**
	 * Execute a Git command as a task.
	 *
	 * @param string $command
	 * @return bool
	 */
	public function executeCommand( string $command): bool
	{
		exec( $command, $output, $exitCode );
		return $exitCode === 0;
	}

}