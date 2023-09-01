<?php

namespace NIIT\PHPTinker;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Psy\ExecutionClosure;
use Psy\Shell;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class Executor
{
    public function __invoke(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('php-tinker::index');
        }

        $code = $request->input('code');
        $status = 1;

        chdir(base_path());
        $output = new BufferedOutput();
        $shell = new Shell();
        $app = app();

        // Set up Laravel context
        $app->singleton('app', function () use ($app) {
            return $app;
        });

        // Initialize PsySH with Laravel context
        $shell->setScopeVariables([
            'app' => $app,
        ]);

        $output->setDecorated(true);
        $shell->setOutput($output);

        try {
            $shell->addCode(
                $this->clearCode($code)
            );

            $closure = new ExecutionClosure($shell);
            $closure->execute();

            $response = preg_replace('/\\x1b\\[\\d+m/', '', $output->fetch());
        } catch (\Throwable $e) {
            $status = 0;
            $response = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'response' => $this->dump($response),
        ]);
    }

    protected function clearCode($code)
    {
        $cleanCode = '';

        if (strncmp($code, '<?php', 5) === 0) {
            $code = array_reverse(explode('<?php', $code, 2))[0];
        }

        foreach (token_get_all('<?php ' . $code) as $token) {
            if (is_string($token)) {
                $cleanCode .= $token;

                continue;
            }

            $cleanCode .= in_array($token[0], [T_COMMENT, T_DOC_COMMENT]) ? '' : $token[1];
        }

        if (strncmp($cleanCode, '<?php', 5) === 0) {
            $cleanCode = array_reverse(explode('<?php', $cleanCode, 2))[0];
        }

        return trim($cleanCode);
    }

    protected function dump(mixed $arguments, int $maxDepth = null): mixed
    {
        if (is_null($arguments)) {
            return null;
        }

        if (is_string($arguments)) {
            return $arguments;
        }

        if (is_int($arguments)) {
            return $arguments;
        }

        if (is_bool($arguments)) {
            return $arguments;
        }

        $varCloner = new VarCloner();

        $dumper = new HtmlDumper();

        if ($maxDepth !== null) {
            $dumper->setDisplayOptions([
                'maxDepth' => $maxDepth,
            ]);
        }

        $htmlDumper = (string)$dumper->dump($varCloner->cloneVar($arguments), true);

        return Str::cut($htmlDumper, '<pre ', '</pre>');
    }
}
