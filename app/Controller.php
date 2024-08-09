<?php

namespace app;

class Controller
{

    protected function view($view, $data = [])
    {
        ob_start();
        require_once "../views/$view.php";
        $content = ob_get_clean();
        $this->renderTemplate($content, $data);
    }

    public function renderTemplate($template, $data = [])
        {
            // Extract data array to variables
            extract($data);

            // Replace {{ }} with PHP echo escaped
            $template = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>', $template);

            // Replace {!! !!} with PHP echo unescaped
            $template = preg_replace('/\{\!\!\s*(.+?)\s*\!\!\}/', '<?php echo $1; ?>', $template);

            // Evaluate the PHP code
            eval('?>' . $template);
        }

}
