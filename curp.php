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
            '01' => ['nombre' => 'AGUASCALIENTES',      'abrev' => 'AS'],
            '02' => ['nombre' => 'BAJA CALIFORNIA',     'abrev' => 'BC'],
            '03' => ['nombre' => 'BAJA CALIFORNIA SUR', 'abrev' => 'BS'],
            '04' => ['nombre' => 'CAMPECHE',            'abrev' => 'CC'],
            '05' => ['nombre' => 'COAHUILA',            'abrev' => 'CL'],
            '06' => ['nombre' => 'COLIMA',              'abrev' => 'CM'],
            '07' => ['nombre' => 'CHIAPAS',             'abrev' => 'CS'],
            '08' => ['nombre' => 'CHIHUAHUA',           'abrev' => 'CH'],
            '09' => ['nombre' => 'DISTRITO FEDERAL',    'abrev' => 'DF'],
            '10' => ['nombre' => 'DURANGO',             'abrev' => 'DG'],
            '11' => ['nombre' => 'GUANAJUATO',          'abrev' => 'GT'],
            '12' => ['nombre' => 'GUERRERO',            'abrev' => 'GR'],
            '13' => ['nombre' => 'HIDALGO',             'abrev' => 'HG'],
            '14' => ['nombre' => 'JALISCO',             'abrev' => 'JC'],
            '15' => ['nombre' => 'ESTADO DE MEXICO',    'abrev' => 'MC'],
            '16' => ['nombre' => 'MICHOACAN',           'abrev' => 'MN'],
            '17' => ['nombre' => 'MORELOS',             'abrev' => 'MS'],
            '18' => ['nombre' => 'NAYARIT',             'abrev' => 'NT'],
            '19' => ['nombre' => 'NUEVO LEON',          'abrev' => 'NL'],
            '20' => ['nombre' => 'OAXACA',              'abrev' => 'OC'],
            '21' => ['nombre' => 'PUEBLA',              'abrev' => 'PL'],
            '22' => ['nombre' => 'QUERETARO',           'abrev' => 'QT'],
            '23' => ['nombre' => 'QUINTANA ROO',        'abrev' => 'QR'],
            '24' => ['nombre' => 'SAN LUIS POTOSI',     'abrev' => 'SP'],
            '25' => ['nombre' => 'SINALOA',             'abrev' => 'SL'],
            '26' => ['nombre' => 'SONORA',              'abrev' => 'SR'],
            '27' => ['nombre' => 'TABASCO',             'abrev' => 'TC'],
            '28' => ['nombre' => 'TAMAULIPAS',          'abrev' => 'TS'],
            '29' => ['nombre' => 'TLAXCALA',            'abrev' => 'TL'],
            '30' => ['nombre' => 'VERACRUZ',            'abrev' => 'VZ'],
            '31' => ['nombre' => 'YUCATAN',             'abrev' => 'YN'],
            '32' => ['nombre' => 'ZACATECAS',           'abrev' => 'ZS'],
            '87' => ['nombre' => 'DOBLE NACIONALIDAD',  'abrev' => 'NE'],
            '88' => ['nombre' => 'NACIDO EXTRANJERO O NATURALIZADO', 'abrev' => 'NE'],
            # '89' => ['nombre':'NATURALIZADO', 'abrev': ''],
        ];
        $this->unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', /*'Ñ'=>'N',*/ 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', /*'ñ'=>'n',*/ 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        $this->nombres = $this->cambiar_letras_indeseadas(trim($nombres));
        $this->apellido_paterno = $this->cambiar_letras_indeseadas(trim($apellido_paterno));
        $this->apellido_materno = $this->cambiar_letras_indeseadas(trim($apellido_materno));
        $this->fecha_nacimiento = $this->cambiar_letras_indeseadas(trim($fecha_nacimiento));
        $this->genero = mb_strtoupper(trim($genero));
        $this->num_entidad = trim($num_entidad);
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
        switch(mb_strtoupper($name))
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
        $palabra = mb_strtoupper($palabra);
        $array   = preg_split('//u', mb_substr($palabra, 0, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);

        for ($i = 0; $i < count($array); $i++)
        {
            $array[$i] = mb_strtoupper(strtr($array[$i], $this->unwanted_array));
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
         * Primer carácter alfabético del primer apellido.
         * Si el carácter es la letra "Ñ", entonces se
         * asigna la letra "X" en su lugar.
         */
        $array_ap = preg_split('//u', mb_substr($apellido_paterno, 0, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);
        $curp     = mb_strtoupper($array_ap[0]) === 'Ñ' ? 'X' 
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
         * Si el carácter es la letra "Ñ", entonces se
         * asigna la letra "X" en su lugar.
         * Si no tine segundo apellido entonces asigna
         * la letra "X" en su lugar.
         */
        $array_am = preg_split('//u', mb_substr($apellido_materno, 0, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);

        if ( ! empty($array_am) && mb_strtoupper($array_am[0]) !== 'Ñ')
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

        $curp = $curp . (mb_strtoupper($primer_nombre_primer_caracter) === 'Ñ' ? 'X' 
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
        $array_ap = preg_split('//u', mb_substr($apellido_paterno, 1, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);

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
        $array_am = preg_split('//u', mb_substr($apellido_materno, 1, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);

        foreach($array_am as $letter)
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
        $aux      = 'X';
        $primer_nombre_letras = preg_split('//u', mb_substr($lista_nombres[0], 1, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($primer_nombre_letras as $letter)
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

        // Dígito verificador.
        $curp = $curp . $this->digito_verificador(mb_strtoupper($curp));

        /**
         * Reemplaza 'Ñ' con 'X'.
         */
        $curp = mb_strtoupper($curp);
        $curp = str_replace('Ñ', 'X', $curp);

        /**
         * Remueve posible palabra altisonante.
         */
        $curp = $this->cambiar_palabra_altisonante($curp);

        return $curp;
    }

    /**
     * Comparación de CURPs.
     * 
     * Códigos de validación:
     *      LONGITUD
     *      AP_PRIMER_CARACTER 
     *      AP_PRIMER_CARACTER_N
     *      AP_VOCAL_NO_INICIAL
     *      ALTISONANTE
     *      AM_PRIMER_CARACTER
     *      AM_PRIMER_CARACTER_N
     *      AM_VACIO
     *      NOMBRE_PRIMER_CARACTER  
     *      FECHA_NAC_ANIO
     *      FECHA_NAC_MES
     *      FECHA_NAC_DIA
     *      GENERO                
     *      ENTIDAD
     *      AP_CONSONANTE_NO_INICIAL
     *      AM_CONSONANTE_NO_INICIAL
     *      NOMBRE_CONSONANTE_NO_INICIAL
     *      HOMOCLAVE_HASTA_1999 
     *      HOMOCLAVE_DESDE_2000   
     *      DIGITO_VERIFICADOR     
     * 
     * @param  string $curp           CURP que se comparará con la interna.
     * @param  Array  $omitir_codigos Arreglo de códigos que no se validarán 
     *                                al comparar.
     * @return Array  Diferencias encontradas.
     */
    function comparar($curp, $omitir_codigos = [])
    {
        $codigos = [
            "LONGITUD"                 => ["mensaje" => "Longitud de CURP incorrecta. Debe tener 18 caracteres."],
            "AP_PRIMER_CARACTER"       => ["mensaje" => 'El primer carácter del apellido paterno es incorrecto.'],
            "AP_PRIMER_CARACTER_N"     => ["mensaje" => 'El primer carácter del apellido paterno debe ser una "X" en vez de "Ñ".'],
            "AP_VOCAL_NO_INICIAL"      => ["mensaje" => 'La primera vocal no inicial del apellido paterno es incorrecto.'],
            "ALTISONANTE"              => ["mensaje" => 'La primera vocal no inicial del apellido paterno es incorrecto debido a palabra altisonante.'],
            "AM_PRIMER_CARACTER"       => ["mensaje" => 'El primer carácter del apellido materno es incorrecto.'],
            "AM_PRIMER_CARACTER_N"     => ["mensaje" => 'El primer carácter del apellido materno es incorrecto. '
                                                     . 'Debe ser "X" cuando comience con la letra "Ñ".'],
            "AM_VACIO"                 => ["mensaje" => 'El primer carácter del apellido materno es incorrecto. '
                                                     . 'Debe ser "X" cuando no tenga apellido materno.'],
            "NOMBRE_PRIMER_CARACTER"   => ["mensaje" => "El primer carácter del primer nombre es incorrecto."],
            "FECHA_NAC_ANIO"           => ["mensaje" => "El año de la fecha de nacimiento es incorrecto."],
            "FECHA_NAC_MES"            => ["mensaje" => "El mes de la fecha de nacimiento es incorrecto."],
            "FECHA_NAC_DIA"            => ["mensaje" => "El día de la fecha de nacimiento es incorrecto."],
            "GENERO"                   => ["mensaje" => 'El género es incorrecto. Debe ser la letra "H" en caso de ser hombre, o la letra "M" en caso de ser mujer.'],
            "ENTIDAD"                  => ["mensaje" => "La clave de la entidad federativa de nacimiento es incorrecta."],
            "AP_CONSONANTE_NO_INICIAL" => ["mensaje" => "La primera consonante no inicial del apellido paterno es incorrecta."],
            "AM_CONSONANTE_NO_INICIAL" => ["mensaje" => "La primera consonante no inicial del apellido materno es incorrecta."],
            "NOMBRE_CONSONANTE_NO_INICIAL" => ["mensaje" => "La primera consonante no inicial del nombre es incorrecta."],
            "HOMOCLAVE_HASTA_1999"     => ["mensaje" => "La homoclave es incorrecta. Debe ser un número del 1 al 9 para fechas de nacimiento hasta el año 1999."],
            "HOMOCLAVE_DESDE_2000"     => ["mensaje" => "La homoclave es incorrecta. Debe ser un carácter de la A a la Z para fechas de nacimiento a partir del año 2000 (alfanumérica)."],
            "DIGITO_VERIFICADOR"       => ["mensaje" => "El digito verificador es incorrecto. Deber se un número del 1 al 9."],
        ];
        $curp                 = mb_strtoupper(trim($curp));
        $curp_actual          = mb_strtoupper($this->curp);
        $curp_actual_letras   = preg_split('//u', mb_substr($curp_actual, 0, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);
        $curp_letras          = preg_split('//u', mb_substr($curp, 0, null, 'utf-8'), -1, PREG_SPLIT_NO_EMPTY);
        $curp_longitud        = strlen($curp);
        $detalles             = [];
        // Rellena espacios vacíos, si existiesen, en la
        // CURP a comparar.
        $curp_letras          = array_pad($curp_letras, 18, "_");
        $caracter1_diferente  = $curp_letras[0]  !== $curp_actual_letras[0];
        $caracter2_diferente  = $curp_letras[1]  !== $curp_actual_letras[1];
        $caracter3_diferente  = $curp_letras[2]  !== $curp_actual_letras[2];
        $caracter4_diferente  = $curp_letras[3]  !== $curp_actual_letras[3];
        $caracter5_diferente  = $curp_letras[4]  !== $curp_actual_letras[4];
        $caracter6_diferente  = $curp_letras[5]  !== $curp_actual_letras[5];
        $caracter7_diferente  = $curp_letras[6]  !== $curp_actual_letras[6];
        $caracter8_diferente  = $curp_letras[7]  !== $curp_actual_letras[7];
        $caracter9_diferente  = $curp_letras[8]  !== $curp_actual_letras[8];
        $caracter10_diferente = $curp_letras[9]  !== $curp_actual_letras[9];
        $caracter11_diferente = $curp_letras[10] !== $curp_actual_letras[10];
        $caracter12_diferente = $curp_letras[11] !== $curp_actual_letras[11];
        $caracter13_diferente = $curp_letras[12] !== $curp_actual_letras[12];
        $caracter14_diferente = $curp_letras[13] !== $curp_actual_letras[13];
        $caracter15_diferente = $curp_letras[14] !== $curp_actual_letras[14];
        $caracter16_diferente = $curp_letras[15] !== $curp_actual_letras[15];
        $caracter17_diferente = $curp_letras[16] !== $curp_actual_letras[16];
        $caracter18_diferente = $curp_letras[17] !== $curp_actual_letras[17];
        $valida = true;

        /**
         * Validación de longitud.
         */
        if (!in_array("LONGITUD", $omitir_codigos)
            && $curp_longitud !== 18)
        {
            $array_indices = [];

            if ($curp_longitud < 18)
            {
                $array_indices = range($curp_longitud + 1, 18);
            }
            else if ($curp_longitud > 18)
            {
                $array_indices = range(19, $curp_longitud);
            }

            $detalles["LONGITUD"] = $codigos["LONGITUD"];
            $detalles["LONGITUD"]["indices"] = $array_indices;

            $valida = false;
        }

        /**
         * Primer carácter alfabético del primer apellido.
         * Si el carácter es la letra "Ñ", entonces debe
         * tener asignado la letra "X" en su lugar.
         * También se verifica palabra altisonante, ya
         * que esta posición es la que se cambia por 'X'
         * en caso de haber coincidencia.
         */
        if ($caracter1_diferente)
        {
            $mensaje = '';

            if (!in_array("AP_PRIMER_CARACTER_N", $omitir_codigos)
                && $curp_letras[0] === 'Ñ' 
                && $curp_actual_letras[0] === "X")
            {
                $detalles["AP_PRIMER_CARACTER_N"] = $codigos["AP_PRIMER_CARACTER_N"];
                $detalles["AP_PRIMER_CARACTER_N"]["indices"] = [0];
                $valida = false;
            }
            else if (!in_array("AP_PRIMER_CARACTER", $omitir_codigos))
            {
                $detalles["AP_PRIMER_CARACTER"] = $codigos["AP_PRIMER_CARACTER"];
                $detalles["AP_PRIMER_CARACTER"]["indices"] = [0];
                $valida = false;
            }
        }

        /**
         * Primer vocal no inicial del primer apellido.
        */
        if ($caracter2_diferente)
        {
            $mensaje = '';

            if (!in_array("ALTISONANTE", $omitir_codigos)
                && $curp_actual_letras[1] === "X" 
                && $this->cambiar_palabra_altisonante($curp) !== $curp)
            {
                $detalles["ALTISONANTE"] = $codigos["ALTISONANTE"];
                $detalles["ALTISONANTE"]["indices"] = [1];
                $valida = false;
            }
            else if (!in_array("AP_VOCAL_NO_INICIAL", $omitir_codigos))
            {
                $detalles["AP_VOCAL_NO_INICIAL"] = $codigos["AP_VOCAL_NO_INICIAL"];
                $detalles["AP_VOCAL_NO_INICIAL"]["indices"] = [1];
                $valida = false;
            }
        }

        /**
         * Primer carácter alfabético del segundo apellido.
         * Si el carácter es la letra "Ñ" o si no tine segundo 
         * apellido, entonces asigna la letra "X" en su lugar.
         */
        if ($caracter3_diferente)
        {
            $mensaje = '';

            if ($curp_actual_letras[2] === "X")
            {
                if (!in_array("AM_VACIO", $omitir_codigos)
                    && empty($this->apellido_materno))
                {
                    $detalles["AM_VACIO"] = $codigos["AM_VACIO"];
                    $detalles["AM_VACIO"]["indices"] = [2];
                    $valida = false;
                }
                else if (!in_array("AM_PRIMER_CARACTER_N", $omitir_codigos))
                {
                    $detalles["AM_PRIMER_CARACTER_N"] = $codigos["AM_PRIMER_CARACTER_N"];
                    $detalles["AM_PRIMER_CARACTER_N"]["indices"] = [2];
                    $valida = false;
                }
            }
            else if (!in_array("AM_PRIMER_CARACTER", $omitir_codigos)) 
            {
                $detalles["AM_PRIMER_CARACTER"] = $codigos["AM_PRIMER_CARACTER"];
                $detalles["AM_PRIMER_CARACTER"]["indices"] = [2];
                $valida = false;
            }
        }

        /**
         * Primer carácter alfabético del primer nombre (en caso de José, María, 
         * J, J., Ma, Ma., se empleara el segundo nombre si lo hubiera).
         */
        if (!in_array("NOMBRE_PRIMER_CARACTER", $omitir_codigos)
            && $caracter4_diferente)
        {
            $detalles["NOMBRE_PRIMER_CARACTER"] = $codigos["NOMBRE_PRIMER_CARACTER"];
            $detalles["NOMBRE_PRIMER_CARACTER"]["indices"] = [3];
            $valida = false;
        }

        /**
         * Dos últimos dígitos del año de nacimiento.
         */
        if (!in_array("FECHA_NAC_ANIO", $omitir_codigos)
            && ($caracter5_diferente || $caracter6_diferente))
        {
            $detalles["FECHA_NAC_ANIO"] = $codigos["FECHA_NAC_ANIO"];
            $detalles["FECHA_NAC_ANIO"]["indices"] = [4, 5];
            $valida = false;
        }
        
        /**
         * Dos dígitos del mes de nacimiento.
         */
        if (!in_array("FECHA_NAC_MES", $omitir_codigos)
            && ($caracter7_diferente || $caracter8_diferente))
        {
            $detalles["FECHA_NAC_MES"] = $codigos["FECHA_NAC_MES"];
            $detalles["FECHA_NAC_MES"]["indices"] = [6, 7];
            $valida = false;
        }

        /**
         * Dos dígitos del día de nacimiento.
         */
        if (!in_array("FECHA_NAC_DIA", $omitir_codigos)
            && ($caracter9_diferente || $caracter10_diferente))
        {
            $detalles["FECHA_NAC_DIA"] = $codigos["FECHA_NAC_DIA"];
            $detalles["FECHA_NAC_DIA"]["indices"] = [8, 9];
            $valida = false;
        }

        /**
         * Carácter H o M para indecar el género Hombre o Mujer segun corresponda.
         */
        if (!in_array("GENERO", $omitir_codigos)
            && $caracter11_diferente)
        {
            $detalles["GENERO"] = $codigos["GENERO"];
            $detalles["GENERO"]["indices"] = [10];
            $valida = false;
        }

        /**
         * Dos caracteres alfabeticos correspondiente a la clave de la entidad
         * federativa de nacimiento.
         */
        if (!in_array("ENTIDAD", $omitir_codigos)
            && ($caracter12_diferente || $caracter13_diferente))
        {
            $detalles["ENTIDAD"] = $codigos["ENTIDAD"];
            $detalles["ENTIDAD"]["indices"] = [11, 12];
            $valida = false;
        }

        /**
         * Primer consonante no inicial del primer apellido.
         */
        if (!in_array("AP_CONSONANTE_NO_INICIAL", $omitir_codigos)
            && $caracter14_diferente)
        {
            $detalles["AP_CONSONANTE_NO_INICIAL"] = $codigos["AP_CONSONANTE_NO_INICIAL"];
            $detalles["AP_CONSONANTE_NO_INICIAL"]["indices"] = [13];
            $valida = false;
        }

        /**
         * Primer consonante no inicial del segundo apellido.
         */
        if (!in_array("AM_CONSONANTE_NO_INICIAL", $omitir_codigos)
            && $caracter15_diferente)
        {
            $detalles["AM_CONSONANTE_NO_INICIAL"] = $codigos["AM_CONSONANTE_NO_INICIAL"];
            $detalles["AM_CONSONANTE_NO_INICIAL"]["indices"] = [14];
            $valida = false;
        }

         /**
         * Primer consonante no inicial del nombre.
         */
        if (!in_array("NOMBRE_CONSONANTE_NO_INICIAL", $omitir_codigos)
            && $caracter16_diferente)
        {
            $detalles["NOMBRE_CONSONANTE_NO_INICIAL"] = $codigos["NOMBRE_CONSONANTE_NO_INICIAL"];
            $detalles["NOMBRE_CONSONANTE_NO_INICIAL"]["indices"] = [15];
            $valida = false;
        }

        /**
         * Homoclave.
         */
        if ($caracter17_diferente)
        {
            if (!in_array("HOMOCLAVE_HASTA_1999", $omitir_codigos)
                && $curp_actual_letras[16] === '0' 
                && !is_numeric($curp_letras[16]))
            {
                $detalles["HOMOCLAVE_HASTA_1999"] = $codigos["HOMOCLAVE_HASTA_1999"];
                $detalles["HOMOCLAVE_HASTA_1999"]["indices"] = [16];
                $valida = false;
            }
            else if (!in_array("HOMOCLAVE_DESDE_2000", $omitir_codigos)
                    && $curp_actual_letras[16] === 'A' 
                    && is_numeric($curp_letras[16]))
            {
                $detalles["HOMOCLAVE_DESDE_2000"] = $codigos["HOMOCLAVE_DESDE_2000"];
                $detalles["HOMOCLAVE_DESDE_2000"]["indices"] = [16];
                $valida = false;
            };
        }

        /**
         * Dígito verificador.
         */
        if ($caracter18_diferente)
        {
            if (!in_array("DIGITO_VERIFICADOR", $omitir_codigos)
                && !is_numeric($curp_letras[17]))
            {
                $detalles["DIGITO_VERIFICADOR"] = $codigos["DIGITO_VERIFICADOR"];
                $detalles["DIGITO_VERIFICADOR"]["indices"] = [17];
                $valida = false;
            }
        }

        return [
            "curp_valida"     => $valida,
            "fallos"          => $detalles,
            "curp_formateada" => $this->remarcarDiferencias($curp_actual_letras, $curp_letras),
        ];
    }

    /**
     * Valida si la curp que se pasa como parámetro es igual
     * a la generada por la clase.
     * 
     * @param  string  $curp                CURP a verificar.
     * @param  boolean $comparacion_ligera  Si es verdadero valida que toda
     *                                      la CURP sea idéntica, de lo 
     *                                      contrario solo valida que sean
     *                                      iguales los primeros 16 caracteres.
     * @return boolean Retorna "true" si las CURP son 
     *                 iguales, de lo contrario retorna
     *                 "false";
     */
    // function igualA($curp, $comparacion_ligera = true)
    // {
    //     $curp     = mb_strtoupper(trim($curp));
    //     $es_igual = false;

    //     if ( ! $comparacion_ligera)
    //     {
    //         $es_igual = $this->curp === $curp;
    //     }
    //     else 
    //     {
    //         $es_igual = mb_substr($curp, 0, 16, 'utf-8') === mb_substr($this->curp, 0, 16, 'utf-8');
    //     }

    //     return $es_igual;
    // }

    /**
     * Función que devuelve la CURP con las posiciones remarcadas
     * donde hay diferencia.
     * 
     * @param Array    $curp_actual_letras Arreglo de caracteres de
     *                                     la CURP generada por la
     *                                     clase Curp.
     * @param Array    $curp_letras        Arreglo de caracteres de
     *                                     la CURP ingresada.
     * @return string  CURP con diferencias remarcadas.
     */
    private function remarcarDiferencias($curp_actual_letras, $curp_letras)
    {
        $curp_formateada = [];
        $apertura        = false;
        $cierre          = false;
        $longitud        = count($curp_letras) > 18 ? count($curp_letras) : 18;

        for ($i = 0; $i < $longitud; $i++) 
        { 
            // Si es diferente el carácter, la posición 
            // es inválida.
            if ( ! isset($curp_letras[$i]) 
                // Caracteres de más.
                || ($longitud > 18 && $i > 17)
                // Diferente carácter.
                || $curp_actual_letras[$i] !== $curp_letras[$i])
            {
                if ( ! $apertura)
                {
                    $apertura = true;

                    $curp_formateada[] = "[";
                }

                $curp_formateada[] = "?";
            }
            // Carácter igual.
            else 
            {
                if ($apertura)
                {
                    $apertura = false;

                    $curp_formateada[] = "]";
                }

                $curp_formateada[] = $curp_letras[$i];
            }
        }

        // Si está abierto corchete, se cierra.
        if ($apertura)
        {
            $curp_formateada[] = "]";
        }

        return implode('', $curp_formateada);
    }
}
