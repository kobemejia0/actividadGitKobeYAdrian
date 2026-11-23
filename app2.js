//reduce: recorre el array y te devuelve un unico valor
let sumaHoras = lenguajes.reduce((acumulador, valor) => acumulador + valor.horas, 0);
console.log(sumaHoras);

//
let cursos = lenguajes.reduce((acumulador, valor) => acumulador + " "+  valor.nombre, "");
console.log(cursos);


//Arrays de funciones
let arrayFunciones = [
    () => console.log("Primera funcion"),
    () => console.log("Segunda funcion"),
    () => console.log("Tercrera funcion"),
    () => console.log("Cuarta funcion")
];

arrayFunciones.forEach(elemento => elemento());

//Objeto set: es una colección especial: "conjunto de valores sin claves", donde cada calor solo puede aparecer solo una vez

let repetidos = ["MADRID", "SORIA", "CUENCA", "LOGROÑO", "ZARAGOZA", "MADRID", "SORIA", "CUENCA"];
//comprobaciones de valores ya existentes en el array
let sinRepetir = new Set(repetidos);
console.log(sinRepetir);
//Comprobar el tamaño
console.log(sinRepetir.size);

//convertir set a array

//1 forma
let arrayDesdeSet = Array.from(sinRepetir);
console.log(arrayDesdeSet);
//2 forma
let arrayDesdeSet1 = [...sinRepetir];
console.log(arrayDesdeSet1);

//añadir valores al set
sinRepetir.add("TERUEL");
console.log(sinRepetir);
//es case sensitive
sinRepetir.add("teruel"); //se añade
console.log(sinRepetir);

//clear(): eliminar todos los elementos del set
//sinRepetir.clear();
//console.log(sinRepetir);

//delete(): elimina el elemento

sinRepetir.delete("MADRID");
console.log(sinRepetir);

//has(): nos dice si el elemento existe o no
console.log(sinRepetir.has("MADRID"));

//union(): join (unir elementos no repetidos)

let a = new Set(['a','b','c']);
let b = new Set(['b','c','d']);

let c = a.union(b);
console.log(c)

//Introducir valores desde el teclado --Practicar
let x = prompt("Introduzca un número");
console.log(x);

//cambios
console.log("Cambios realizados");