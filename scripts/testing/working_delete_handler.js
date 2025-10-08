// Let me create a simple working delete function to replace the broken one

// Simple working delete handler
} else if (action === "delete") {
    console.log("Delete action triggered for course:", c.course_name);

    // Simple prompt-based confirmation
    const userInput = prompt(
        `⚠️ DELETE CONFIRMATION ⚠️\n\nYou are about to permanently delete:\n"${c.course_name}"\n\nThis action CANNOT be undone.\n\nType 'DELETE' to confirm:`
    );

    console.log("User input:", userInput);

    if (userInput !== "DELETE") {
        console.log("Delete cancelled - user did not type DELETE");
        if (userInput === null) {
            console.log("User clicked Cancel");
        } else if (userInput !== "") {
            alert("Deletion cancelled. You must type 'DELETE' exactly to confirm.");
        }
        return;
    }

    console.log("User confirmed with DELETE - starting delete process...");
    setIsLoading(true);

    try {
        const response = await fetch(`/admin/course-dates/${c.id}`, {
            method: "DELETE",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN":
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "",
            },
        });

        const result = await response.json();

        if (response.ok && result.success) {
            if ((window as any).toastr) {
                (window as any).toastr.success(
                    result.message || `Course deleted: ${c.course_name}`
                );
            } else {
                alert(result.message || `Course deleted: ${c.course_name}`);
            }
            onDeleteCourse?.(c);
            onRefreshData && setTimeout(onRefreshData, 300);
        } else {
            throw new Error(result.message || "Failed to delete course");
        }
    } catch (error) {
        console.error("Delete error:", error);
        const message = error instanceof Error ? error.message : "Failed to delete course";

        if ((window as any).toastr) {
            (window as any).toastr.error(message);
        } else {
            alert(message);
        }
    } finally {
        console.log("Delete process completed");
        setIsLoading(false);
    }
}
