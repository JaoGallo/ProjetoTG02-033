import pkg from 'xlsx';
const { readFile, utils } = pkg;

const workbook = readFile('Gallo - Dados Atdr 2026.xlsx');
const sheetName = workbook.SheetNames[0];
const worksheet = workbook.Sheets[sheetName];
const data = utils.sheet_to_json(worksheet, { header: 1 });

const headers = data[0];
console.log("Headers:");
headers.forEach((h, i) => console.log(`${i}: ${h}`));

console.log("\nSample Data (Row 2):");
console.log(JSON.stringify(data[1], null, 2));
