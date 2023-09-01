<?php

namespace NIIT\PHPTinker;

use Illuminate\Http\Request;
use Psy\ExecutionClosure;
use Psy\Shell;
use Symfony\Component\Console\Output\BufferedOutput;

class Executor
{
    public function __invoke(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('php-tinker::index');
        }

        $code = $request->input('code');
        $status = 1;

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
            $shell->addCode($this->clearCode($code));

            $closure = new ExecutionClosure($shell);
            $closure->execute();

            $response = preg_replace('/\\x1b\\[\\d+m/', '', $output->fetch());
        } catch (\Throwable $e) {
            $status = 0;
            $response = "Error: " . $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'response' => $response,
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
            if (in_array($token[0], [\T_COMMENT, \T_DOC_COMMENT])) {
                $cleanCode .= '';
            } else {
                $cleanCode .= $token[1];
            }
        }
        if (strncmp($cleanCode, '<?php', 5) === 0) {
            $cleanCode = array_reverse(explode('<?php', $cleanCode, 2))[0];
        }
        return $cleanCode;
    }
}
