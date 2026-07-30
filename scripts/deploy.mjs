import { dirname, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawn, spawnSync } from 'node:child_process';

const themeDir = dirname(dirname(fileURLToPath(import.meta.url)));
const remoteHost = 'u943866697@46.202.182.18';
const remotePort = '65002';
const remoteThemeDir = '/home/u943866697/domains/echelonmotions.net/public_html/wp-content/themes/car-rental-theme/';
const args = process.argv.slice(2);
const dryRun = args[0] === '--dry-run';

if (args.length > 1 || (args.length === 1 && !dryRun)) {
  console.error('Usage: npm run deploy -- [--dry-run]');
  process.exit(2);
}

const rsyncOptions = [
  '--archive',
  '--compress',
  '--verbose',
  '--exclude=.git/',
  '--exclude=node_modules/',
  '--exclude=.DS_Store',
];

const tarOptions = [
  '--exclude=.git',
  '--exclude=node_modules',
  '--exclude=.DS_Store',
];

function commandExists(command) {
  const check = process.platform === 'win32'
    ? spawnSync('where', [command], { stdio: 'ignore' })
    : spawnSync('sh', ['-c', `command -v ${command}`], { stdio: 'ignore' });

  return check.status === 0;
}

function run(command, commandArgs, options = {}) {
  const result = spawnSync(command, commandArgs, {
    cwd: themeDir,
    stdio: 'inherit',
    shell: process.platform === 'win32' && command === 'npm',
    ...options,
  });

  if (result.error) {
    console.error(result.error.message);
    process.exit(1);
  }

  if (result.status !== 0) {
    process.exit(result.status ?? 1);
  }
}

function quoteRemotePath(value) {
  return `'${value.replaceAll("'", "'\\''")}'`;
}

function themeDirForRsync() {
  return themeDir.endsWith(sep) ? themeDir : `${themeDir}${sep}`;
}

function deployWithTar() {
  if (!commandExists('tar')) {
    console.error('rsync is not installed, and tar is required for the fallback deploy.');
    process.exit(1);
  }

  if (!commandExists('ssh')) {
    console.error('rsync is not installed, and ssh is required for the fallback deploy.');
    process.exit(1);
  }

  if (dryRun) {
    console.log('rsync is not installed; validating fallback archive only.');
    run('tar', ['-czf', process.platform === 'win32' ? 'NUL' : '/dev/null', ...tarOptions, '-C', themeDir, '.']);
    return Promise.resolve();
  }

  console.log('rsync is not installed; deploying with tar over ssh.');
  run('ssh', ['-p', remotePort, remoteHost, `mkdir -p ${quoteRemotePath(remoteThemeDir)}`]);

  return new Promise((resolve, reject) => {
    const tar = spawn('tar', ['-czf', '-', ...tarOptions, '-C', themeDir, '.'], {
      stdio: ['ignore', 'pipe', 'inherit'],
    });
    const ssh = spawn('ssh', ['-p', remotePort, remoteHost, `tar -xzf - -C ${quoteRemotePath(remoteThemeDir)}`], {
      stdio: ['pipe', 'inherit', 'inherit'],
    });

    tar.stdout.pipe(ssh.stdin);

    tar.on('error', reject);
    ssh.on('error', reject);
    tar.on('close', (code) => {
      if (code !== 0) {
        ssh.kill();
        reject(new Error(`tar exited with code ${code}`));
      }
    });
    ssh.on('close', (code) => {
      if (code === 0) {
        resolve();
      } else {
        reject(new Error(`ssh exited with code ${code}`));
      }
    });
  });
}

run('npm', ['run', 'build']);

if (commandExists('rsync')) {
  const deployArgs = dryRun ? [...rsyncOptions, '--dry-run'] : rsyncOptions;
  run('rsync', [
    ...deployArgs,
    '-e',
    `ssh -p ${remotePort}`,
    themeDirForRsync(),
    `${remoteHost}:${remoteThemeDir}`,
  ]);
} else {
  deployWithTar().catch((error) => {
    console.error(error.message);
    process.exit(1);
  });
}
