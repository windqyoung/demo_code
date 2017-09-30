<?php

namespace Wqy;

use PDO;

class ProcessMakerCaseDoc
{
    /**
     * @var PDO
     */
    private $pdo;

    private $proUid;

    private $dynaformVars;

    private $ignoreFields;

    public function __construct($pdo, $proUid)
    {
        $this->pdo = $pdo;
        $this->proUid = $proUid;
    }

    public function genBySteps()
    {
        $fp = fopen($this->proUid . '_steps.csv', 'w');
        stream_copy_to_stream($this->genByStepsStream(), $fp);
        fclose($fp);
    }

    public function genByStepsStream()
    {
        $dyn = $this->getDynaformVars();

        $handle= fopen('php://temp', 'w');

        $steps = $this->getSteps();
        foreach ($steps as $one) {

            fputcsv($handle, [$this->getTaskName($one['TAS_UID'])]);

            foreach ($dyn as $dy) {

                if (isset($dy['forms'][$one['STEP_UID_OBJ']])) {
                    $csvData = [
                        $dy['var']['var_name'],
                        $dy['var']['var_field_type'],
                    ];

                    foreach ($dy['fields'] as $fd) {
                        $csvData[] = $fd['label'] . '(' . implode(', ', array_map(function ($v) {
                            return $v['name'];
                        }, $fd['*forms'])) . ')';
                    }
                    fputcsv($handle, $csvData);
                }
            }

        }

        rewind($handle);

        return $handle;
    }


    public function getTaskName($tasUid)
    {
        $stmt = $this->pdo->prepare('select tas_title from task where tas_uid = ?');
        $stmt->execute([$tasUid]);
        return $stmt->fetchColumn();
    }

    public function getDynaformVars()
    {
        if ($this->dynaformVars) {
            return $this->dynaformVars;
        }

        $dynStmt = $this->pdo->prepare('select * from dynaform where pro_uid = ?');
        $dynStmt->execute([$this->proUid]);
        $dyns = $dynStmt->fetchAll(PDO::FETCH_ASSOC);

        $vars = [];
        foreach ($dyns as $d) {
            $data = json_decode($d['DYN_CONTENT'], true);
            $this->walkDynaform($data, $vars);
        }

        return $this->dynaformVars = $vars;
    }

    /**
     * @start
     */
    public function walkDynaform($dynContent, & $vars)
    {
        if (empty($dynContent['items'])) {
            return;
        }

        foreach ($dynContent['items'] as $one) {
            if ($one['type'] == 'form') {
                $this->walkForm($one, $vars);
            }
            else {
                throw new \Exception('unexpect no form item');
            }
        }
    }

    public function walkForm($form, & $vars, $parent = [])
    {
        $this->processFormVars($form, $vars, $parent);
        $this->processFormItems($form, $vars, $parent);
    }

    private function processFormVars($form, & $vars, $parent = [])
    {
        if (empty($form['variables'])) {
            return;
        }

        foreach ($form['variables'] as $var) {
            if (empty($vars[$var['var_name']]['vars'])) {
                $vars[$var['var_name']]['var'] = $var;
                $vars[$var['var_name']]['var_unused'] = [];
            }
            else {
                $vars[$var['var_name']]['var_unused'][] = $var;
            }

            $varForms = array_filter(array_merge([$form], $parent));
            foreach ($varForms as $aForm) {
                // 表单在哪些form中使用过
                if (empty($vars[$var['var_name']]['forms'][$aForm['id']])) {
                    $vars[$var['var_name']]['forms'][$aForm['id']] = $this->filterForm($aForm);
                    $vars[$var['var_name']]['forms_unused'] = [];
                }
                else {
                    $vars[$var['var_name']]['forms_unused'][] = $this->filterForm($aForm);
                }
            }

        }
    }

    private function processFormItems($form, & $vars, $parent = [])
    {
        if (empty($form['items'])) {
            return;
        }
        // 行
        foreach ($form['items'] as $row) {
            // 列
            foreach ($row as $col) {
                if (isset($col['type']) && $col['type'] == 'form') {
                    $tempParent = $parent;
                    $tempParent[] = $this->filterForm($form);
                    $this->walkForm($col, $vars, $tempParent);
                }
                else if (isset($col['variable'])) {
                    $col['*forms'] = array_merge([$this->filterForm($form)], $parent);
                    $vars[$col['variable']]['fields'][] = $col;
                }
                else {
                    $this->ignoreFields[] = $col;
                }
            }
        }
    }

    private function filterForm($form)
    {
        foreach (['script', 'items', 'variables', 'sql', ] as $key) {
            unset($form[$key]);
        }

        return $form;
    }

    public function getSteps()
    {
        $stmt = $this->pdo->prepare("select * from step where pro_uid = ? and step_type_obj = 'DYNAFORM'");
        $stmt->execute([$this->proUid]);
        $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $steps;
    }
}



function fputcsv($handle, $fields)
{
    $fields = array_map(function ($one) {
        return iconv('utf-8', 'gbk', $one);
    }, $fields);
    \fputcsv($handle, $fields);
}
