<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');
    
class Restapi_Model extends Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function setUserSessionBySessionIdModel($sessionId) {
        
        $userRow = self::getUserSessionInfoBySessionIdModel($sessionId);
        
        if ($userRow) {
            return self::setUserSessionModel($userRow);
        }
        
        return null;
    }
    
    public function setUserSessionByUserPassModel($username, $password) {
        
        $userRow = self::getUserSessionInfoByUserPassModel($username, $password);
        
        if ($userRow) {
            return self::setUserSessionModel($userRow);
        }
        
        return null;
    }
    
    public function setUserSessionModel($userRow) {
        
        $sessionValues = ['sessioncompanydepartmentid' => $userRow['COMPANY_DEPARTMENT_ID']];

        Session::init();

        Session::set(SESSION_PREFIX . 'LoggedIn', true);
        Session::set(SESSION_PREFIX . 'userkeyid', $userRow['USER_ID']);
        Session::set(SESSION_PREFIX . 'userid', $userRow['SYSTEM_USER_ID']);
        Session::set(SESSION_PREFIX . 'username', $userRow['USERNAME']);
        Session::set(SESSION_PREFIX . 'firstname', $userRow['FIRST_NAME']);
        Session::set(SESSION_PREFIX . 'personname', $userRow['FIRST_NAME']);
        Session::set(SESSION_PREFIX . 'employeeid', $userRow['EMPLOYEE_ID']);
        Session::set(SESSION_PREFIX . 'employeekeyid', $userRow['EMPLOYEE_KEY_ID']);
        Session::set(SESSION_PREFIX . 'departmentid', $userRow['DEPARTMENT_ID']);
        Session::set(SESSION_PREFIX . 'customerid', $userRow['CUSTOMER_ID']);
        Session::set(SESSION_PREFIX . 'sessionValues', $sessionValues);
        Session::set(SESSION_PREFIX . 'periodStartDate', Date::currentDate('Y-m') . '-01');

        return true;
    }
    
    public function getUserSessionInfoBySessionIdModel($sessionId) {
        
        try {
            
            $row = $this->db->GetRow("
                SELECT 
                    US.USER_ID, 
                    US.SYSTEM_USER_ID, 
                    US.EMPLOYEE_ID, 
                    US.EMPLOYEE_KEY_ID, 
                    US.DEPARTMENT_ID, 
                    US.COMPANY_DEPARTMENT_ID, 
                    US.CUSTOMER_ID, 
                    USU.USERNAME, 
                    BP.FIRST_NAME 
                FROM UM_USER_SESSION US 
                    INNER JOIN UM_SYSTEM_USER USU ON USU.USER_ID = US.SYSTEM_USER_ID 
                    LEFT JOIN BASE_PERSON BP ON BP.PERSON_ID = USU.PERSON_ID 
                WHERE US.SESSION_ID = ".$this->db->Param(0), 
                [$sessionId]
            );

            return $row;
        
        } catch (Exception $ex) { 
            return [];
        }
    }
    
    public function getUserSessionInfoByUserPassModel($username, $password) {
        
        try {
            
            $username = Str::lower($username);
            $oldHash  = Hash::createMD5reverse($password);
            $newHash  = Hash::create('sha256', $password);

            $usernamePh = $this->db->Param(0);
            $oldpassPh  = $this->db->Param(1);
            $newpassPh  = $this->db->Param(2);

            $row = $this->db->GetRow("
                SELECT 
                    UM.USER_ID, 
                    USU.USER_ID AS SYSTEM_USER_ID, 
                    VU.EMPLOYEE_ID, 
                    VU.EMPLOYEE_KEY_ID, 
                    VU.DEPARTMENT_ID, 
                    UM.COMPANY_DEPARTMENT_ID, 
                    NULL AS CUSTOMER_ID, 
                    USU.USERNAME, 
                    VU.FIRST_NAME 
                FROM UM_SYSTEM_USER USU 
                    LEFT JOIN UM_USER UM ON UM.SYSTEM_USER_ID = USU.USER_ID 
                    LEFT JOIN VW_USER VU ON VU.USER_ID = USU.USER_ID 
                WHERE LOWER(USU.USERNAME) = $usernamePh 
                    AND (USU.PASSWORD_HASH = $oldpassPh OR USU.PASSWORD_HASH = $newpassPh)", 
                [$username, $oldHash, $newHash]
            );

            return $row;
        
        } catch (Exception $ex) {
            return [];
        }
    }
    
    public function runIndicatorFromMetaProcessDataModel($param, $jsonParam) {
        
        $bpId         = $param['_metadataid'];
        $indicatorIds = $param['_indicatorid'];
        
        $indicators = $this->db->GetAll("
            SELECT 
                T0.SRC_INDICATOR_ID AS INDICATOR_ID, 
                T1.QUERY_STRING 
            FROM KPI_INDICATOR_INDICATOR_MAP T0 
                INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.SRC_INDICATOR_ID 
            WHERE T0.SEMANTIC_TYPE_ID = 78 
                AND T0.LOOKUP_META_DATA_ID = $bpId 
                AND T0.SRC_INDICATOR_ID IN ($indicatorIds) 
                AND T1.QUERY_STRING IS NOT NULL 
            ORDER BY T0.ORDER_NUMBER ASC");
        
        if ($indicators) {
            
            $this->load->model('mdform', 'middleware/models/');
            
            $runIndicators = [];
            
            foreach ($indicators as $indicator) {
                
                $indicatorId = $indicator['INDICATOR_ID'];
                
                if (!isset($runIndicators[$indicatorId])) {
                    
                    $queryString = $indicator['QUERY_STRING'];
                    
                    if (mb_strlen($queryString) > 30) {
                        
                        includeLib('Compress/Compression');
                        
                        $queryString = Compression::decompress($queryString);
                        $queryString = trim($queryString);
                        $queryString = $this->model->replaceKpiDbSchemaName($queryString);
                        $first7Char  = strtolower(substr($queryString, 0, 7));
                        $matches     = DBSql::getQueryNamedParams($queryString);
                        
                        $params = $this->db->GetAll("
                            SELECT 
                                LOWER(PM.SRC_INDICATOR_PATH) AS SRC_PATH, 
                                LOWER(PM.TRG_META_DATA_PATH) AS TRG_PATH 
                            FROM KPI_INDICATOR_INDICATOR_MAP M 
                                LEFT JOIN KPI_INDICATOR_INDICATOR_MAP PM ON M.ID = PM.SRC_INDICATOR_MAP_ID 
                            WHERE M.SEMANTIC_TYPE_ID = 78 
                                AND PM.TRG_META_DATA_PATH IS NOT NULL 
                                AND M.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                                AND M.LOOKUP_META_DATA_ID = ".$this->db->Param(1)." 
                            GROUP BY  
                                PM.SRC_INDICATOR_PATH, 
                                PM.TRG_META_DATA_PATH", 
                            [$indicatorId, $bpId]
                        );
                        
                        $tmpParams = $bindParams = [];
                        
                        foreach ($params as $row) {
                            $bpPath = $row['TRG_PATH'];
                            $qryPath = $row['SRC_PATH'];
                            
                            $tmpParams[$qryPath] = isset($param[$bpPath]) ? $param[$bpPath] : null;
                        }
                        
                        foreach ($matches as $matchParam) {
                            $matchParam = str_replace(':', '', strtolower($matchParam));
                            
                            if (isset($tmpParams[$matchParam])) {
                                $bindParams[$matchParam] = $tmpParams[$matchParam];
                            } else {
                                $bindParams[$matchParam] = null;
                            }
                        }
                        
                        $logParam = [
                            'indicatorId' => $indicatorId, 
                            'affectedRows' => null, 
                            'executedQuery' => $queryString."\n\n".$jsonParam."\n\n".json_encode($bindParams), 
                            'errorMsg' => null
                        ];
                        
                        try {
                            
                            /*if ($first7Char == 'declare') {
                                
                                $keys = array_map('strlen', array_keys($bindParams));
                                array_multisort($keys, SORT_DESC, $bindParams);
                                
                                foreach ($bindParams as $bindParam => $bindVal) {
                                    $queryString = str_ireplace(':'.$bindParam, $bindVal, $queryString);
                                }
                                
                                $this->db->Execute($queryString);
                                
                            } else {*/
                            
                                $this->db->Execute($queryString, $bindParams);
                                $affectedRows = $this->db->affected_rows();
                                
                                $logParam['affectedRows'] = $affectedRows;
                                
                                self::insertCheckQueryLogModel($logParam);
                            //}
                            
                        } catch (Exception $ex) { 
                            
                            $logParam['errorMsg'] = $ex->getMessage();
                            self::insertCheckQueryLogModel($logParam);
                        }
                    }
                    
                    $runIndicators[$indicatorId] = 1;
                }
            }
            
            $response = array('status' => 'success');
        } else {
            $response = array('status' => 'error', 'message' => 'No indicators!');
        }
        
        return $response;
    }
    
    public function insertCheckQueryLogModel($param) {
        
        try {
            
            $errorMsg = $param['errorMsg'];
            $errorMsgLength = mb_strlen($errorMsg);
            
            $data = [
                'LOG_ID'       => getUID(), 
                'INDICATOR_ID' => $param['indicatorId'], 
                'ERROR_QTY'    => $param['affectedRows'], 
                'CREATED_DATE' => Date::currentDate()
            ];
            
            if ($errorMsgLength < 4000) {
                $data['LOG_MESSAGE'] = $errorMsg;
            }
            
            $rs = $this->db->AutoExecute('V_CHECK_QUERY_EXECUTED_LOG', $data);
            
            if ($errorMsgLength > 4000) {
                $this->db->UpdateClob('V_CHECK_QUERY_EXECUTED_LOG', 'LOG_MESSAGE', $errorMsg, 'LOG_ID = '.$data['LOG_ID']);
            }
            
            if ($rs) {
                $this->db->UpdateClob('V_CHECK_QUERY_EXECUTED_LOG', 'EXECUTED_QUERY', $param['executedQuery'], 'LOG_ID = '.$data['LOG_ID']);
                return true;
            }
            
        } catch (Exception $ex) { }
        
        return false;
    }
    
    public function checkMetaVerseRuleDiscountModel($metaProcessId, $parameters) {
        
        try {
            
            $data = $this->db->GetAll("
                SELECT 
                    T0.ID AS MAP_ID, 
                    T1.ID, 
                    T2.EXPRESSION 
                FROM KPI_INDICATOR_INDICATOR_MAP T0 
                    INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.SRC_INDICATOR_ID 
                    INNER JOIN V_RULE_CONFIG T2 ON T2.SRC_RECORD_ID = T1.ID 
                WHERE T0.LOOKUP_META_DATA_ID = ".$this->db->Param(0)." 
                    AND T0.SEMANTIC_TYPE_ID = 7025 
                    AND T2.EXPRESSION IS NOT NULL 
                ORDER BY T0.ORDER_NUMBER ASC", 
                [$metaProcessId]
            );
            
            if ($data) {
                
                $checkAlreadyRule = $ruleList = $metricIds = $mapIds = [];
                
                if (isset($parameters['jsonparam'])) {
                    $parameters = Arr::changeKeyLower(json_decode($parameters['jsonparam'], true));
                }
                
                foreach ($data as $row) {
                    
                    if (isset($checkAlreadyRule[$row['ID']])) {
                        continue;
                    }
                    
                    $expression = Str::lower($row['EXPRESSION']);
                    $expression = html_entity_decode($expression, ENT_QUOTES, 'UTF-8');
                    
                    if (strpos($expression, '[') !== false) {
                        
                        preg_match_all('/\[([\w\W]*?)\]/i', $expression, $expressionArr);
                        
                        if (count($expressionArr[0]) > 0) {
                            
                            foreach ($expressionArr[1] as $ek => $ev) {
                                
                                $evArr = explode('.', $ev);
                                
                                if ($evArr && count($evArr) == 2) {
                                    
                                    $metricId = trim($evArr[0]);
                                    $metricIds[$metricId] = 1;
                                }
                            }
                        }
                    }
                    
                    $ruleList[] = ['ruleId' => $row['ID'], 'expression' => $expression];
                    $checkAlreadyRule[$row['ID']] = 1;
                    $mapIds[] = $row['MAP_ID'];
                }
                
                $metricExecute = $executedRuleIds = [];
                $discountPolicyIds = '';
                
                if ($metricIds) {
                    
                    $metricList = $this->db->GetAll("
                        SELECT 
                            SRC_RECORD_ID, 
                            QUERY_STRING 
                        FROM V_METRIC_CONFIG 
                        WHERE SRC_RECORD_ID IN (".implode(',', array_keys($metricIds)).") 
                            AND QUERY_STRING IS NOT NULL");
                    
                    $paramMap = $this->db->GetAll("
                        SELECT 
                            LOWER(SRC_INDICATOR_PATH) AS SRC_INDICATOR_PATH, 
                            LOWER(TRG_META_DATA_PATH) AS TRG_META_DATA_PATH 
                        FROM KPI_INDICATOR_INDICATOR_MAP 
                        WHERE SRC_INDICATOR_MAP_ID IN (".implode(',', $mapIds).") 
                        GROUP BY SRC_INDICATOR_PATH, TRG_META_DATA_PATH");
                    
                    foreach ($metricList as $metricRow) {
                        
                        $metricId        = $metricRow['SRC_RECORD_ID'];
                        $metricQryString = html_entity_decode($metricRow['QUERY_STRING'], ENT_QUOTES, 'UTF-8');
                        $namedParams     = DBSql::getQueryNamedParams($metricQryString);
                        $metricQryTmp    = $metricQryString;
                        
                        $tmpParams = $bindParams = [];
                        
                        foreach ($paramMap as $row) {
                            $bpPath = $row['TRG_META_DATA_PATH'];
                            $qryPath = $row['SRC_INDICATOR_PATH'];
                            
                            $tmpParams[$qryPath] = isset($parameters[$bpPath]) ? $parameters[$bpPath] : null;
                        }
                        
                        foreach ($namedParams as $matchParam) {
                            $matchParam = str_replace(':', '', strtolower($matchParam));
                            
                            if (isset($tmpParams[$matchParam])) {
                                $bindParams[$matchParam] = $tmpParams[$matchParam];
                            } else {
                                $bindParams[$matchParam] = null;
                            }
                            
                            $metricQryTmp = str_ireplace(':'.$matchParam, 'null', $metricQryTmp);
                        }
                        
                        try {
                            $metricResultRow = $this->db->GetRow($metricQryString, $bindParams);
                            
                            if ($metricResultRow) {
                                $metricExecute[$metricId] = Arr::changeKeyLower($metricResultRow);
                            } else {
                                $this->load->model('mdform', 'middleware/models/');
                                
                                $metricQryTmp = '('.$metricQryTmp.')';
                                $dbColumns = $this->model->table_exists($this->db, $metricQryTmp);
                                
                                if ($dbColumns) {
                                    foreach ($dbColumns as $dbColumn => $dbColumnType) {
                                        $metricExecute[$metricId] = [strtolower($dbColumn) => 0];
                                    }
                                } else {
                                    $metricExecute[$metricId] = [];
                                }
                            }

                        } catch (Exception $ex) {
                            $metricExecute[$metricId] = [];
                        }
                    }
                }
                
                foreach ($ruleList as $ruleRow) {
                    
                    $ruleId         = $ruleRow['ruleId'];
                    $ruleExpression = $ruleRow['expression'];
                    
                    if (strpos($ruleExpression, '[') !== false) {
                        
                        preg_match_all('/\[([\w\W]*?)\]/i', $ruleExpression, $expressionArr);
                        
                        if (count($expressionArr[0]) > 0) {
                            
                            foreach ($expressionArr[1] as $ek => $ev) {
                                
                                $evArr = explode('.', $ev);
                                
                                if ($evArr && count($evArr) == 2) {
                                    
                                    $metricId = trim($evArr[0]);
                                    $metricPath = trim($evArr[1]);
                                    
                                    if (isset($metricExecute[$metricId][$metricPath])) {
                                        $ruleExpression = str_replace($expressionArr[0][$ek], Str::lower($metricExecute[$metricId][$metricPath]), $ruleExpression);
                                    }
                                }
                            }
                        }
                    }
                    
                    if (Mdcommon::expressionEvalFixWithReturn($ruleExpression)) {
                        $executedRuleIds[] = $ruleId;
                    }
                }
                
                if ($executedRuleIds) {
                    
                    $discountPolicy = $this->db->GetAll("
                        SELECT 
                            WD.ID 
                        FROM META_DM_RECORD_MAP MAP 
                            INNER JOIN WH_DISCOUNT_POLICY WD ON MAP.SRC_RECORD_ID = WD.ID 
                        WHERE MAP.SRC_REF_STRUCTURE_ID = 1464077549513 
                            AND MAP.TRG_REF_STRUCTURE_ID = 17152437259563 
                            AND MAP.TRG_RECORD_ID IN (".implode(',', $executedRuleIds).")");
                    
                    $discountPolicyIds = Arr::implode_key(',', $discountPolicy, 'ID', true);
                }
                
                $response = [
                    'status' => 'success', 
                    'ruleList' => $ruleList, 
                    'metricIds' => $metricIds, 
                    'executedRuleIds' => $executedRuleIds, 
                    'discountPolicyIds' => $discountPolicyIds
                ];
                
            } else {
                $response = ['status' => 'error', 'message' => 'No config!'];
            }
            
        } catch (Exception $ex) {
            $response = ['status' => 'error', 'message' => $ex->getMessage()];
        }
        
        return $response;
    }
    
}