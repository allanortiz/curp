#!/usr/bin/python
# -*- coding: utf8 -*-

class Curp:

    def __init__(self, nombres, apellido_paterno, apellido_materno,
        fecha_nacimiento, genero, num_entidad):
        self.vowels = u'aeiou'
        self.consonants = u'bcdfghjklmnñpqrstvwxyz'
        self.entidades = {
            #'0': {'nombre':'?', 'abrev': ''},
            '1': {'nombre':'AGUASCALIENTES', 'abrev': 'AS'},
            '2': {'nombre':'BAJA CALIFORNIA', 'abrev': 'BC'},
            '3': {'nombre':'BAJA CALIFORNIA SUR', 'abrev': 'BS'},
            '4': {'nombre':'CAMPECHE', 'abrev': 'CC'},
            '5': {'nombre':'COAHUILA', 'abrev': 'CL'},
            '6': {'nombre':'COLIMA', 'abrev': 'CM'},
            '7': {'nombre':'CHIAPAS', 'abrev': 'CS'},
            '8': {'nombre':'CHIHUAHUA', 'abrev': 'CH'},
            '9': {'nombre':'DISTRITO FEDERAL', 'abrev': 'DF'},
            '10': {'nombre':'DURANGO', 'abrev': 'DG'},
            '11': {'nombre':'GUANAJUATO', 'abrev': 'GT'},
            '12': {'nombre':'GUERRERO', 'abrev': 'GR'},
            '13': {'nombre':'HIDALGO', 'abrev': 'HG'},
            '14': {'nombre':'JALISCO', 'abrev': 'JC'},
            '15': {'nombre':'ESTADO DE MEXICO', 'abrev': 'MC'},
            '16': {'nombre':'MICHOACAN', 'abrev': 'MN'},
            '17': {'nombre':'MORELOS', 'abrev': 'MS'},
            '18': {'nombre':'NAYARIT', 'abrev': 'NT'},
            '19': {'nombre':'NUEVO LEON', 'abrev': 'NL'},
            '20': {'nombre':'OAXACA', 'abrev': 'OC'},
            '21': {'nombre':'PUEBLA', 'abrev': 'PL'},
            '22': {'nombre':'QUERETARO', 'abrev': 'QT'},
            '23': {'nombre':'QUINTANA ROO', 'abrev': 'QR'},
            '24': {'nombre':'SAN LUIS POTOSI', 'abrev': 'SP'},
            '25': {'nombre':'SINALOA', 'abrev': 'SL'},
            '26': {'nombre':'SONORA', 'abrev': 'SR'},
            '27': {'nombre':'TABASCO', 'abrev': 'TC'},
            '28': {'nombre':'TAMAULIPAS', 'abrev': 'TS'},
            '29': {'nombre':'TLAXCALA', 'abrev': 'TL'},
            '30': {'nombre':'VERACRUZ', 'abrev': 'VZ'},
            '31': {'nombre':'YUCATAN', 'abrev': 'YN'},
            '32': {'nombre':'ZACATECAS', 'abrev': 'ZS'},
            '87': {'nombre':'DOBLE NACIONALIDAD', 'abrev': 'NE'},
            '88': {'nombre':'NACIDO EXTRANJERO O NATURALIZADO', 'abrev': 'NE'},
            # '89': {'nombre':'NATURALIZADO', 'abrev': ''},
        }
        self.nombres = nombres.lower()
        self.apellido_paterno = apellido_paterno.lower()
        self.apellido_materno = apellido_materno.lower()
        self.fecha_nacimiento = fecha_nacimiento.lower()
        self.genero = genero.upper()
        self.num_entidad = num_entidad

        self.curp = self.generar(self.nombres, self.apellido_paterno,
            self.apellido_materno, self.fecha_nacimiento, self.genero,
            self.num_entidad)

    def digito_verificador(self, curp):
        contador = 18
        count = 0
        valor = 0
        sumaria = 0

        verificadores = {
            u'0':0, u'1':1, u'2':2, u'3':3, u'4':4, u'5':5, u'6':6, u'7':7, u'8':8, u'9':9,
            u'A':10, u'B':11, u'C':12, u'D':13, u'E':14, u'F':15, u'G':16, u'H':17, u'I':18,
            u'J':19, u'K':20, u'L':21, u'M':22, u'N':23, u'Ñ':24, u'O':25, u'P':26, u'Q':27,
            u'R':28, u'S':29, u'T':30, u'U':31, u'V':32, u'W':33, u'X':34, u'Y':35, u'Z':36
        }

        for count in range(0, len(curp)):
            posicion = curp[count]

            for key,value in verificadores.items():
                if posicion.encode('utf-8') == key.encode('utf-8'):
                    valor = (value * contador)

            contador = contador - 1
            sumaria = sumaria + valor

        # Sacar el residuo
        num_ver = sumaria % 10
        # Devuelve el valor absoluto en caso de que sea negativo
        num_ver = abs(10 - num_ver)
        # En caso de que sea 10 el digito es 0
        if num_ver == 10:
            num_ver = 0

        return str(num_ver)

    def generar(self, nombres, apellido_paterno, apellido_materno,
        fecha_nacimiento, genero, num_entidad):
        lista_nombres = nombres.split(' ')
        nacimiento = str(fecha_nacimiento).split('-')
        dia_nacimiento = nacimiento[2]
        mes_nacimiento = nacimiento[1]
        anio_nacimiento = nacimiento[0]

        # Si el primer nombre es jose o maria, y tiene más de un nombre,
        # se remueve el primer nombre.
        if len(lista_nombres) > 1 and (lista_nombres[0] == 'jose'
                                        or lista_nombres[0] == 'maria'):
            del lista_nombres[0]

        def is_invalid_name(name):
            if (name == 'de'
                or name == 'del'
                or name == 'los'
                or name == 'las'
                or name == 'la'
                or name == 'el'
                or name == 'y'):
                return True

            return False

        # Remueve partes del nombre que no toma en cuenta la CURP (articulos, etc.).
        lista_nombres = [name for name in lista_nombres if not is_invalid_name(name)]

        # Primer carácter alfabético del primer apellido.
        curp = apellido_paterno[0]

        # Primer vocal no inicial del primer apellido.
        aux = 'X'

        for letter in apellido_paterno[1:]:
            if letter in self.vowels:
                aux = letter
                break

        curp = curp + aux

        # Primer carácter alfabético del segundo apellido.
        curp = curp + apellido_materno[0]

        # Primer carácter alfabético del primer nombre, en caso de José o María
        # se empleara el segundo nombre si lo hubiera.
        # if len(lista_nombres) > 1 and ('jose' in lista_nombres or 'maria' in lista_nombres):
        #     curp = curp + lista_nombres[1][0]
        # else:
        #     curp = curp + lista_nombres[0][0]
        curp = curp + lista_nombres[0][0]

        # Dos últimos dígitos del año de nacimiento.
        curp = curp + (str(anio_nacimiento)[len(anio_nacimiento) - 2:])

        # Dos dígitos del mes de nacimiento
        curp = curp + mes_nacimiento

        # Dos dígitos del día de nacimiento
        curp = curp + dia_nacimiento

        # Carácter H o M para indecar el género Hombre o Mujer segun corresponda.
        curp = curp + genero

        # Valida que la entidad se encuentre en la lista.
        if str(num_entidad) not in self.entidades:
            return ''

        # Dos caracteres alfabeticos correspondiente a la clave de la entidad
        # federativa de nacimiento.
        curp = curp + self.entidades[str(num_entidad)]['abrev']

        # Primer consonante no inicial del primer apellido.
        aux = 'X'

        for letter in apellido_paterno[1:]:
            if letter in self.consonants:
                aux = letter
                break

        curp = curp + aux

        # Primer consonante no inicial del segundo apellido.
        aux = 'X'

        for letter in apellido_materno[1:]:
            if letter in self.consonants:
                aux = letter
                break

        curp = curp + aux

        # Primer consonante no inicial del nombre.
        aux = 'X'

        for letter in lista_nombres[0][1:]:
            if letter in self.consonants:
                aux = letter
                break

        curp = curp + aux

        # Dos dígitos para evitar duplicidades:
        # Homoclave.
        curp = curp + ('0' if int(anio_nacimiento) < 2000 else 'A')

        # Dígito verificador.
        curp = curp + self.digito_verificador(curp.upper())

        # Reemplaza 'Ñ' con 'X'.
        curp = curp.upper()
        curp = curp.replace(u'Ñ', 'X')

        return curp
