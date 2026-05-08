import * as XLSX from 'xlsx/xlsx.mjs';
import * as fs from 'fs';
XLSX.set_fs(fs);

const workbook = XLSX.readFile('Escala Sv + Adt.xlsx');
workbook.SheetNames.forEach(sheetName => {
    console.log(`--- Sheet: ${sheetName} ---`);
    const worksheet = workbook.Sheets[sheetName];
    const data = XLSX.utils.sheet_to_json(worksheet);
    console.log(JSON.stringify(data.slice(0, 5), null, 2)); // Just first 5 rows
});
