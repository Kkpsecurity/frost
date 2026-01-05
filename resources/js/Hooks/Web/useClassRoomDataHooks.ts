import { useMutation } from "@tanstack/react-query";

function getCsrfToken(): string {
    const el = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    return el?.content ?? "";
}

async function postFormData(url: string, formData: FormData) {
    const csrf = getCsrfToken();

    const res = await fetch(url, {
        method: "POST",
        headers: {
            Accept: "application/json",
            ...(csrf ? { "X-CSRF-TOKEN": csrf } : {}),
        },
        credentials: "include",
        body: formData,
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok || data?.success === false) {
        const message = data?.message || "Upload failed";
        throw new Error(message);
    }

    return data;
}

/**
 * `HandelFileUpload` (legacy spelling preserved)
 *
 * Expects a FormData that includes:
 * - `course_date_id`
 * - `photoType` ("idcard" | "headshot")
 * - `file` (Blob/File)
 *
 * Other fields may be present but are ignored by the endpoints.
 */
export function HandelFileUpload() {
    return useMutation({
        mutationFn: async (payload: FormData) => {
            const photoType = String(payload.get("photoType") ?? "");
            const courseDateId = payload.get("course_date_id");
            const file = payload.get("file");

            if (!courseDateId) {
                throw new Error("Missing course_date_id");
            }
            if (!(file instanceof Blob)) {
                throw new Error("Missing file");
            }

            if (photoType === "idcard") {
                const form = new FormData();
                form.append("course_date_id", String(courseDateId));
                form.append("id_document", file);
                return postFormData("/classroom/id-verification/start", form);
            }

            if (photoType === "headshot") {
                const form = new FormData();
                form.append("course_date_id", String(courseDateId));
                form.append("headshot", file);
                return postFormData("/classroom/id-verification/upload-headshot", form);
            }

            throw new Error("Invalid photoType");
        },
    });
}
