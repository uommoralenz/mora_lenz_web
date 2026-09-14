"use client";

export default function GlobalError({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  return (
    <div className="flex min-h-screen items-center justify-center p-6">
      <div className="card w-full max-w-md p-6 text-center">
        <h1 className="text-lg font-semibold text-slate-100">Something went wrong</h1>
        <p className="mt-2 text-sm text-slate-400">
          {error.message ||
            "The panel could not load. This usually means the Mora Lenz server is unreachable."}
        </p>
        <button type="button" onClick={reset} className="btn-primary mt-5">
          Try again
        </button>
      </div>
    </div>
  );
}
