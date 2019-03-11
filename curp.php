<?php 
/**
 * Clase para generación de CURP.
 */
class Curp
{
    /**
     * Constructor.
     */
    public function __construct($nombres, $apellido_paterno, $apellido_materno,
        $fecha_nacimiento, $genero, $num_entidad)
    {
        $this->vowels     = preg_split('//u', 'AEIOU', -1, PREG_SPLIT_NO_EMPTY);
        $this->consonants = preg_split('//u', 'BCDFGHJKLMNÑPQRSTVWXYZ', -1, PREG_SPLIT_NO_EMPTY);
        $this->entidades  = [
            #'0': {'nombre':'?', 'abrev': ''},
            '1' => ['nombre' => 'AGUASCALIENTES', 'abrev' => 'AS'],
            '2' => ['nombre' => 'BAJA CALIFORNIA', 'abrev' => 'BC'],
            '3' => ['nombre' => 'BAJA CALIFORNIA SUR', 'abrev' => 'BS'],
            '4' => ['nombre' => 'CAMPECHE', 'abrev' => 'CC'],
            '5' => ['nombre' => 'COAHUILA', 'abrev' => 'CL'],
            '6' => ['nombre' => 'COLIMA', 'abrev' => 'CM'],
            '7' => ['nombre' => 'CHIAPAS', 'abrev' => 'CS'],
            '8' => ['nombre' => 'CHIHUAHUA', 'abrev' => 'CH'],
            '9' => ['nombre' => 'DISTRITO FEDERAL', 'abrev' => 'DF'],
            '10' => ['nombre' => 'DURANGO', 'abrev' => 'DG'],
            '11' => ['nombre' => 'GUANAJUATO', 'abrev' => 'GT'],
            '12' => ['nombre' => 'GUERRERO', 'abrev' => 'GR'],
            '13' => ['nombre' => 'HIDALGO', 'abrev' => 'HG'],
            '14' => ['nombre' => 'JALISCO', 'abrev' => 'JC'],
            '15' => ['nombre' => 'ESTADO DE MEXICO', 'abrev' => 'MC'],
            '16' => ['nombre' => 'MICHOACAN', 'abrev' => 'MN'],
            '17' => ['nombre' => 'MORELOS', 'abrev' => 'MS'],
            '18' => ['nombre' => 'NAYARIT', 'abrev' => 'NT'],
            '19' => ['nombre' => 'NUEVO LEON', 'abrev' => 'NL'],
            '20' => ['nombre' => 'OAXACA', 'abrev' => 'OC'],
            '21' => ['nombre' => 'PUEBLA', 'abrev' => 'PL'],
            '22' => ['nombre' => 'QUERETARO', 'abrev' => 'QT'],
            '23' => ['nombre' => 'QUINTANA ROO', 'abrev' => 'QR'],
            '24' => ['nombre' => 'SAN LUIS POTOSI', 'abrev' => 'SP'],
            '25' => ['nombre' => 'SINALOA', 'abrev' => 'SL'],
            '26' => ['nombre' => 'SONORA', 'abrev' => 'SR'],
            '27' => ['nombre' => 'TABASCO', 'abrev' => 'TC'],
            '28' => ['nombre' => 'TAMAULIPAS', 'abrev' => 'TS'],
            '29' => ['nombre' => 'TLAXCALA', 'abrev' => 'TL'],
            '30' => ['nombre' => 'VERACRUZ', 'abrev' => 'VZ'],
            '31' => ['nombre' => 'YUCATAN', 'abrev' => 'YN'],
            '32' => ['nombre' => 'ZACATECAS', 'abrev' => 'ZS'],
            '87' => ['nombre' => 'DOBLE NACIONALIDAD', 'abrev' => 'NE'],
            '88' => ['nombre' => 'NACIDO EXTRANJERO O NATURALIZADO', 'abrev' => 'NE'],
            # '89' => ['nombre':'NATURALIZADO', 'abrev': ''],
        ];
        $this->unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', /*'Ñ'=>'N',*/ 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        $this->nombres = $this->cambiar_letras_indeseadas($nombres);
        $this->apellido_paterno = $this->cambiar_letras_indeseadas($apellido_paterno);
        $this->apellido_materno = $this->cambiar_letras_indeseadas($apellido_materno);
        $this->fecha_nacimiento = $this->cambiar_letras_indeseadas($fecha_nacimiento);
        $this->genero = strtoupper($genero);
        $this->num_entidad = $num_entidad;
        $this->curp = $this->generar(
            $this->nombres, 
            $this->apellido_paterno,
            $this->apellido_materno, 
            $this->fecha_nacimiento, 
            $this->genero,
            $this->num_entidad
        );
    }

    /**
     * Calcula el dígito verificador.
     */
    function digito_verificador($curp)
    {
        $curp = preg_split('//u', $curp, -1, PREG_SPLIT_NO_EMPTY);
        $contador = 18;
        $count    = 0;
        $valor    = 0;
        $sumaria  = 0;

        $verificadores = [
            '0' => 0,  '1' => 1,  '2'=> 2,  '3' => 3,  '4' => 4,  '5' =>5,  '6' =>6,  '7' =>7,  '8' => 8, '9' => 9,
            'A' => 10, 'B' => 11, 'C'=> 12, 'D' => 13, 'E' => 14, 'F' =>15, 'G' =>16, 'H' =>17, 'I' => 18,
            'J' => 19, 'K' => 20, 'L'=> 21, 'M' => 22, 'N' => 23, 'Ñ' =>24, 'O' =>25, 'P' =>26, 'Q' => 27,
            'R' => 28, 'S' => 29, 'T'=> 30, 'U' => 31, 'V' => 32, 'W' =>33, 'X' =>34, 'Y' =>35, 'Z' => 36
        ];

        for ($i = 0; $i < count($curp); $i++)
        {
            $posicion = $curp[$i];

            foreach ($verificadores as $key => $value)
            {
                if (utf8_encode($posicion) == utf8_encode($key))
                {
                    $valor = ($value * $contador);
                }
            }

            $contador = $contador - 1;
            $sumaria = $sumaria + $valor;
        }

        # Sacar el residuo
        $num_ver = $sumaria % 10;

        # Devuelve el valor absoluto en caso de que sea negativo
        $num_ver = abs(10 - $num_ver);

        # En caso de que sea 10 el digito es 0
        if ($num_ver == 10)
        {
            $num_ver = 0;
        }

        return strval($num_ver);
    }

    /**
     * Verifica si la palabra del nombre
     * es válida.
     */
    function es_nombre_invalido($name)
    {
        switch(strtoupper($name))
        {
            case 'DA':
            case 'DAS':
            case 'DE':
            case 'DEL':
            case 'DER':
            case 'DI':
            case 'DIE':
            case 'DD':
            case 'EL':
            case 'LA':
            case 'LOS':
            case 'LAS':
            case 'LE':
            case 'LES':
            case 'MAC':
            case 'MC':
            case 'VAN':
            case 'VON':
            case 'Y':
                return true;

                break;
        }

        return false;
    }

    /**
     * Cambia caracteres de acuerdo al listado
     * de letras indeseadas.
     */
    function cambiar_letras_indeseadas($palabra)
    {
        $palabra = strtoupper($palabra);
        $array   = preg_split('//u', mb_substr($palabra, 0, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);

        for ($i = 0; $i < count($array); $i++)
        {
            $array[$i] = strtoupper(strtr($array[$i], $this->unwanted_array));
        }

        return implode("", $array);
    }

    /**
     * Verifica si las 4 primeras letras de la CURP 
     * forman una palabra altisonante o inconveniente,
     * en tal caso modifica la segunda letra con una "X".
     */
    function cambiar_palabra_altisonante($curp)
    {
        $primeras_4_letras = mb_substr($curp, 0, 4 , 'utf-8');

        switch($primeras_4_letras)
        {
            case 'BACA':
            case 'BAKA':
            case 'BUEI':
            case 'BUEY':
            case 'CACA':
            case 'CACO':
            case 'CAGA':
            case 'CAGO':
            case 'CAKA':
            case 'CAKO':
            case 'COGE':
            case 'COGI':
            case 'COJA':
            case 'COJE':
            case 'COJI':
            case 'COJO':
            case 'COLA':
            case 'CULO':
            case 'FALO':
            case 'FETO':
            case 'GETA':
            case 'GUEI':
            case 'GUEY':
            case 'JETA':
            case 'JOTO':
            case 'KACA':
            case 'KACO':
            case 'KAGA':
            case 'KAGO':
            case 'KAKA':
            case 'KAKO':
            case 'KOGE':
            case 'KOGI':
            case 'KOJA':
            case 'KOJE':
            case 'KOJI':
            case 'KOJO':
            case 'KOLA':
            case 'KULO':
            case 'LILO':
            case 'LOCA':
            case 'LOCO':
            case 'LOKA':
            case 'LOKO':
            case 'MAME':
            case 'MAMO':
            case 'MEAR':
            case 'MEAS':
            case 'MEON':
            case 'MIAR':
            case 'MION':
            case 'MOCO':
            case 'MOKO':
            case 'MULA':
            case 'MULO':
            case 'NACA':
            case 'NACO':
            case 'PEDA':
            case 'PEDO':
            case 'PENE':
            case 'PIPI':
            case 'PITO':
            case 'POPO':
            case 'PUTA':
            case 'PUTO':
            case 'QULO':
            case 'RATA':
            case 'ROBA':
            case 'ROBE':
            case 'ROBO':
            case 'RUIN':
            case 'SENO':
            case 'TETA':
            case 'VACA':
            case 'VAGA':
            case 'VAGO':
            case 'VAKA':
            case 'VUEI':
            case 'VUEY':
            case 'WUEI':
            case 'WUEY':

                $curp = substr_replace($curp, "X", 1, 1);

            break;
        }

        return $curp;
    }

    /**
     * Genera la CURP de acuerdo a los datos 
     * ingesados de la persona.
     */
    function generar($nombres, 
        $apellido_paterno, 
        $apellido_materno,
        $fecha_nacimiento, 
        $genero, 
        $num_entidad)
    {
        $lista_nombres   = explode(' ', $nombres);
        $nacimiento      = explode('-', $fecha_nacimiento);
        $dia_nacimiento  = $nacimiento[2];
        $mes_nacimiento  = $nacimiento[1];
        $anio_nacimiento = $nacimiento[0];

        /**
         * Si el primer nombre es jose o maria, y tiene más de un nombre,
         * se remueve el primer nombre.
         */
        if (count($lista_nombres) > 1 && ($lista_nombres[0] == 'JOSE'
                                         || $lista_nombres[0] == 'J.'
                                         || $lista_nombres[0] == 'J'
                                         || $lista_nombres[0] == 'MARIA'
                                         || $lista_nombres[0] == 'MA.'
                                         || $lista_nombres[0] == 'MA'))
        {

            // Elimina el primer elemento de la lista.
            array_shift($lista_nombres);
        }
        /**
         * Remueve partes del nombre que no toma en cuenta la CURP (articulos, etc.).
         */
        $lista_nombres_aux = [];

        for ($i = 0; $i < count($lista_nombres); $i++)
        {
            $name = $lista_nombres[$i];

            if ( ! $this->es_nombre_invalido($name))
            {
                $lista_nombres_aux[] = $name;
            }
        }

        $lista_nombres = $lista_nombres_aux;

        /**
         * Si el apellido es compuesto, verifica que no tenga
         * palabras inválidas.
         */
        $array_ap = explode(" ", $apellido_paterno);
        $array_am = explode(" ", $apellido_materno);
        $array_ap_aux = [];
        $array_am_aux = [];

        // Validación para apellido paterno.
        if (count($array_ap) > 1)
        {
            for ($i = 0; $i < count($array_ap); $i++)
            {
                $apellido = $array_ap[$i];
                
                if ( ! $this->es_nombre_invalido($apellido))
                {
                    $array_ap_aux[] = $apellido;
                }
            }

            $apellido_paterno = implode(" ", $array_ap_aux);
        }

        // Validación para apellido materno.
        if (count($array_am) > 1)
        {
            for ($i = 0; $i < count($array_am); $i++)
            {
                $apellido = $array_am[$i];

                if ( ! $this->es_nombre_invalido($apellido))
                {
                    $array_am_aux[] = $apellido;
                }
            }

            $apellido_materno = implode(" ", $array_am_aux);
        }

        /**
         * Primer caractér alfabético del primer apellido.
         * Si el caractér es la letra "Ñ", entonces se
         * asigna la letra "X" en su lugar.
         */
        $array_ap = preg_split('//u', mb_substr($apellido_paterno, 0, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);
        $curp     = strtoupper($array_ap[0]) === 'Ñ' ? 'X' 
                                                     : $array_ap[0];
                     
        /**
         * Primer vocal no inicial del primer apellido.
        */
        $aux = 'X';

        // Al convertir a arreglo el apellido, se devuelve sin la
        // primera letra ya que no se toma en cuenta.
        $array_ap        = preg_split('//u', mb_substr($apellido_paterno, 1, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);
        $previous_letter = null;

        foreach($array_ap as $letter)
        {
            $letter_aux = strtr($letter, $this->unwanted_array);

            if (in_array($letter_aux, $this->vowels))
            {
                /**
                 * Si en los apellidos o en el nombre aparecieran caracteres
                 * especiales como diagonal (/), guión (-), o punto (.), se captura tal
                 * cual viene en el documento probatorio y la aplicación asignará una
                 * "X" en caso de que esa posición intervenga para la conformación
                 * de la clave.
                 */
                if ($previous_letter !== '/'
                    && $previous_letter !== '-'
                    && $previous_letter !== '.')
                {
                    $aux = $letter_aux;
                }
                
                break;
            }

            $previous_letter = $letter_aux;
        }
                                                            
        $curp = $curp . $aux;

        /**
         * Primer carácter alfabético del segundo apellido.
         * Si el caractér es la letra "Ñ", entonces se
         * asigna la letra "X" en su lugar.
         * Si no tine segundo apellido entonces asigna
         * la letra "X" en su lugar.
         */
        $array_am = preg_split('//u', mb_substr($apellido_materno, 0, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);

        if ( ! empty($array_am) && strtoupper($array_am[0]) !== 'Ñ')
        {
            $curp = $curp . $array_am[0];
        }
        else 
        {
            $curp = $curp . 'X';
        }

        /**
         * Primer carácter alfabético del primer nombre, en caso de José o María
         * se empleara el segundo nombre si lo hubiera.
         */
        $primer_nombre = preg_split('//u', mb_substr($lista_nombres[0], 0, 1, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);
        $primer_nombre_primer_caracter = $primer_nombre[0];

        $curp = $curp . (strtoupper($primer_nombre_primer_caracter) === 'Ñ' ? 'X' 
                                                                  : $primer_nombre_primer_caracter);

        /**
         * Dos últimos dígitos del año de nacimiento.
         */
        $digits = substr(strval($anio_nacimiento), strlen($anio_nacimiento) - 2);
        $curp   = $curp . ($digits);

        
        /**
         * Dos dígitos del mes de nacimiento.
         */
        $curp = $curp . $mes_nacimiento;
        
        /**
         * Dos dígitos del día de nacimiento.
         */
        $curp = $curp . $dia_nacimiento;
        
        /**
         * Carácter H o M para indecar el género Hombre o Mujer segun corresponda.
         */
        $curp = $curp . $genero;

        /**
         * Valida que la entidad se encuentre en la lista.
         */
        if ( ! array_key_exists(strval($num_entidad), $this->entidades))
        {
            return '';
        }

        /**
         * Dos caracteres alfabeticos correspondiente a la clave de la entidad
         * federativa de nacimiento.
         */
        $curp = $curp . $this->entidades[strval($num_entidad)]['abrev'];

        /**
         * Primer consonante no inicial del primer apellido.
         */
        $aux = 'X';

        $array_ap = str_split(substr($apellido_paterno, 1));

        foreach($array_ap as $letter)
        {
            $letter_aux = strtr($letter, $this->unwanted_array);

            if (in_array($letter_aux, $this->consonants))
            {
                $aux = $letter_aux;

                break;
            }
        }

        $curp = $curp . $aux;

        /**
         * Primer consonante no inicial del segundo apellido.
         */
        $aux = 'X';

        foreach(str_split(substr($apellido_materno, 1)) as $letter)
        {
            if (in_array($letter, $this->consonants))
            {
                $aux = $letter;

                break;
            }
        }

        $curp = $curp . $aux;

        /**
         * Primer consonante no inicial del nombre.
         */
        $aux = 'X';

        foreach (str_split(substr($lista_nombres[0], 1)) as $letter)
        {
            if (in_array($letter, $this->consonants))
            {
                $aux = $letter;

                break;
            }
        }

        $curp = $curp . $aux;

        /**
         * Dos dígitos para evitar duplicidades:
         * Homoclave.
         */
        $curp = $curp . (intval($anio_nacimiento) < 2000 ? '0' : 'A');

        /**
         * Dígito verificador.
         */
        $curp = $curp . $this->digito_verificador(strtoupper($curp));

        /**
         * Reemplaza 'Ñ' con 'X'.
         */
        $curp = strtoupper($curp);
        $curp = str_replace('Ñ', 'X', $curp);

        /**
         * Remueve posible palabra altisonante.
         */
        $curp = $this->cambiar_palabra_altisonante($curp);

        return $curp;
    }
}
