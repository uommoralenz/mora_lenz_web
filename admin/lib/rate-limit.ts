import "server-only";
import crypto from "node:crypto";
import { execute, queryRows } from "@/lib/db";

export async function consumeRateLimit(scope: string, identifier: string, limit: number, windowSeconds: number) {
  const bucket = crypto.createHash("sha256").update(`${scope}:${identifier}`).digest("hex");
  const now = Date.now();
  const expires = now + windowSeconds * 1000;
  await execute(
    `INSERT INTO admin_rate_limits (bucket, attempts, expires_at) VALUES (?, 1, ?)
     ON DUPLICATE KEY UPDATE
       attempts = IF(expires_at <= ?, 1, LEAST(attempts + 1, 1000000)),
       expires_at = IF(expires_at <= ?, ?, expires_at)`,
    [bucket, expires, now, now, expires],
  );
  const [row] = await queryRows<{ attempts: number }>("SELECT attempts FROM admin_rate_limits WHERE bucket = ?", [bucket]);
  if (Math.random() < 0.01) await execute("DELETE FROM admin_rate_limits WHERE expires_at <= ? LIMIT 100", [now]);
  return !!row && row.attempts <= limit;
}
