import "server-only";

import mysql, { type ExecuteValues, type Pool, type ResultSetHeader } from "mysql2/promise";

type GlobalWithPool = typeof globalThis & {
  moraLenzPool?: Pool;
};

function databaseUrl() {
  const url = process.env.DATABASE_URL;

  if (!url) {
    throw new Error("DATABASE_URL is not configured.");
  }

  return url;
}

function getPool() {
  const globalForPool = globalThis as GlobalWithPool;

  if (!globalForPool.moraLenzPool) {
    globalForPool.moraLenzPool = mysql.createPool({
      uri: databaseUrl(),
      connectionLimit: Number(process.env.DB_CONNECTION_LIMIT ?? 3),
      dateStrings: false,
      enableKeepAlive: true,
      ssl: process.env.DATABASE_SSL === "true" ? { rejectUnauthorized: true } : undefined,
      timezone: "Z",
      waitForConnections: true,
    });
  }

  return globalForPool.moraLenzPool;
}

export async function queryRows<T>(sql: string, values: ExecuteValues[] = []) {
  const [rows] = await getPool().execute(sql, values);

  return rows as T[];
}

export async function execute(sql: string, values: ExecuteValues[] = []) {
  const [result] = await getPool().execute<ResultSetHeader>(sql, values);

  return result;
}
