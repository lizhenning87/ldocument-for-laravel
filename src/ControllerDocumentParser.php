<?php


namespace Zning\Apidocument;


class ControllerDocumentParser
{

    private $params = array();

    function parse($doc = '') {

        if ($doc == '')
        {
            return $this->params;
        }

        if (preg_match('#^/\*\*(.*)\*/#s', $doc, $comment) === false)
        {
            return $this->params;
        }
        $comment = trim($comment[1]);
        if (preg_match_all('#^\s*\*(.*)#m', $comment, $lines) === false)
        {
            return $this->params;
        }

        $this->parseLines($lines[1]);

        return $this->params;
    }

    private function parseLines($lines) {

        if (!is_array($lines))
        {
            return;
        }

        foreach ($lines as $key => $line)
        {
            $parseLine = $this->parseLine($line);

            if ($parseLine === false && !isset($this->params['description']))
            {
                if (isset($desc))
                {
                    $this->params['description'] = implode(PHP_EOL, $desc);
                }

                $desc = array();
            }elseif ($parseLine !== false)
            {
                $desc[] = $parseLine;
            }
        }

        $desc = implode(' ', $desc);
        if (!empty($desc))
        {
            $this->params['long_description'] = $desc;
        }

    }

    private function parseLine($line) {

        $line = trim($line);

        if (empty($line))
        {
            return false;
        }

        if (strpos($line, '@') === 0)
        {
            if (strpos($line, ' ') > 0)
            {
                $param = substr($line, 1, strpos($line, ' ') - 1);
                $value = substr($line, strlen($param) + 2);

            }else
            {
                $param = substr($line, 1);
                $value = '';
            }

            if ($this->setParam($param, $value))
            {
                return false;
            }

        }

        return $line;
    }

    private function setParam($param, $value) {

        if ($param == 'param' || $param == 'return')
        {
            $value = $this->formatParamOrReturn($value);
        }

        if ($param == 'class')
        {
            list($param, $value) = $this->formatClass($value);
        }

        if (empty($this->params[$param]) && $param != 'q' && $param != 'b' && $param != 'u' && $param != 'r')
        {
            $this->params[$param] = $value;

        }elseif ($param == 'param')
        {
            $arr = array(
                $this->params[$param],
                $value
            );

            $this->params[$param] = $arr;

        }else if ($param == 'q' || $param == 'b' || $param == 'u' || $param == 'r')
        {
            $arr = array_key_exists($param, $this->params) ? $this->params[$param] : [];
            $arr[] = $value;
            $this->params[$param] = $arr;
        }

        return true;
    }


    private function formatClass($value) {
        $r = preg_split("[\(|\)]", $value);
        if (is_array($r))
        {
            $param = $r[0];
            parse_str($r[1], $value);
            foreach ($value as $key => $val)
            {
                $val = explode(',', $val);
                if (count($val) > 1)
                {
                    $value[$key] = $val;
                }
            }

        }else
        {
            $param = 'Unknown';
        }
        return array(
            $param,
            $value,
        );
    }

    private function formatParamOrReturn($string) {
        $pos = strpos($string, ' ');
        $type = substr($string, 0, $pos);

        return '('.$type.')'.substr($string, $pos+1);
    }

}
