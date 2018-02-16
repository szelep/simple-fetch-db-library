<?php
require('db.php');
class database extends connection
    {
    private $query;
    private $table;
    private $elements;
    private $values;
    private $where;
    private $format;
    private $find;
    private $db;

    function __construct() {
        $this->db = $this->dbconnect();
    }

    public function findOne(){

        $this->find = 'row';
        return $this;
    }
    public function findMany(){
        $this->find = 'rows';
        return $this;
    }

    public function setFormat($val){
        $formats = ['json','table','array'];
        if(in_array(strtolower($val),$formats)){
            $this->format = strtolower($val);
            return $this;
        } else{
            return false;
        }
    }
    public function setTable($val)
        {
            $this->table = $val;
            return $this;
        }
        public function setQuery($val)
        {
            $this->query = strtolower($val);
            return $this;
        }

        private $elementsSize;
        public function setElements($val)
        {
            $this->elements = implode(", ",$val);
            $this->elementsSize = sizeof($val);
            return $this;
        }

        private $valuesSize;
        public function setValues($val)
        {
            $this->values = implode('","',$val);
            $this->valuesSize = sizeof($val);
            return $this;
        }

        public function build_query()
        {
            $build = '';
            if($this->query == 'select')
            {
                $build .= $this->query . ' ' . ($this->elements != ''? $this->elements : '*');
                $build .= ' from ' . $this->table;
                $build .= ($this->where != ''? ' where '.$this->where : '');

                if($this->requiredItems('blank',$this->table))
                {
                    return $build;
                } else {
                    return false;
                }

            } else if($this->query == 'insert')
            {

                if($this->requiredItems('blank',$this->table) && $this->requiredItems('blank',$this->elements) && $this->requiredItems('blank',$this->values) && $this->elementsSize == $this->valuesSize){
                    $build .= $this->query . ' into '.$this->table.'('.$this->elements.') values("'.$this->values.'")';
                    return $build;
                } else{
                    return false;
                }
            }
        }

        public function setWhere($val){

            $this->where = implode(" ",$val);

            return $this;
        }

        public function gen_query()
        {
            if(!$this->build_query())
            {
                return 'ERROR: INCORRECT OR MISSING COMPONENTS';
            } else{
                if($this->query == 'select')
                {
                    return $this->fetchData($this->build_query());
                } else if($this->query == 'insert'){
                    return $this->insertData($this->build_query());
                }
            }
        }

        private function insertData($query)
        {
            $this->db->query($query);
        }

        private function fetchData($query)
        {
            $rows = $this->db->query($query);

            $parsed =  $this->parseData($rows);
            $format = $this->format;

           if($rows->num_rows > 0 && $parsed != '') {

               if ($format == 'json')
               {
                   return json_encode($parsed);
               } else if ($format == 'array')
               {
                   return $parsed;
               } else if ($format == 'table')
               {
                    return $this->build_table($parsed);

               } else
                   {
                   return json_encode($parsed);
               }
           }

        }

        public function checkExists($table, $key){
            $query = "SELECT * from $table WHERE $key[0]=$key[1]";
            $rows = $this->db->query($query);

            return ($rows->num_rows > 0? true : false );
        }

        private $rowClass;

        public function setRowClass($val){
            $this->rowClass = $val;
            return $this;
        }


        private function build_table($parsed){


            $html = '<table>';
            if($this->find == 'rows'){
               $keys = array_keys($parsed[0]);
               for($i=0;$i<sizeof($parsed);$i++){
                   $html .= '<tr class="tb_row '.($i%2 == 0? "n_row " : "p_row ").($this->rowClass != ""? $this->rowClass : "").'">';
                    for($e=0;$e<sizeof($parsed[$i]);$e++){
                        $html .= '<td>'.$parsed[$i][$keys[$e]].'</td>';
                    }
                   $html .= '</tr>';
                }


               } else if($this->find == 'row'){
                $keys = array_keys($parsed);
                print_r($keys);
                $html .= '<tr class="tb_row '.($this->rowClass != ""? $this->rowClass : "").'">';
                    for($i=0;$i<sizeof($keys);$i++){
                        $html .= '<td>'.$parsed[$keys[$i]].'</td>';
                    }
                $html .= '</tr>';
            }



            $html .= '</table>';
            return $html;
        }

        private function parseData($rows){
            if($this->find == 'row'){
                $dbresult = mysqli_fetch_assoc($rows);
                if($this->elements != ''){
                    $output_row = [];
                    $elements_list = explode(', ',$this->elements);
                    for($i=0;$i<sizeof($elements_list);$i++){
                        $output_row[$elements_list[$i]] = $dbresult[$elements_list[$i]];
                    }

                        return $output_row;

                } else {
                    return $dbresult;
                }

            } else if($this->find == 'rows'){
                $output_rows = [];

                while ($dbresult = mysqli_fetch_assoc($rows)) {
                    array_push($output_rows, $dbresult);
                }
                 return $output_rows;
            }
        }

        private function requiredItems($query, $value)
        {
            switch ($query) {
                case 'blank' :
                    if ($value != '') { return true; } else { return false; }; break;
            }
        }


    }
?>
