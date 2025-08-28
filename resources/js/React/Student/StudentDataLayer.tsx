import React, { useEffect, useMemo, useState } from "react";
import {
    Menu,
    X,
    Bell,
    Search,
    Home,
    BookOpen,
    Calendar,
    ClipboardList,
    MessageSquare,
    Settings,
    LogOut,
} from "lucide-react";

// Minimal in-file UI atoms so this runs standalone in preview.
function IconButton({
    title,
    onClick,
    children,
}: {
    title?: string;
    onClick?: () => void;
    children: React.ReactNode;
}) {
    return (
        <button
            title={title}
            onClick={onClick}
            className="inline-flex items-center justify-center rounded-2xl px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
        >
            {children}
        </button>
    );
}

function ToolbarDivider() {
    return <div className="mx-2 h-6 w-px bg-gray-200 dark:bg-gray-700" />;
}

const navItems = [
    { icon: <Home className="h-5 w-5" />, label: "Overview" },
    { icon: <BookOpen className="h-5 w-5" />, label: "Courses" },
    { icon: <Calendar className="h-5 w-5" />, label: "Schedule" },
    { icon: <ClipboardList className="h-5 w-5" />, label: "Assignments" },
    { icon: <MessageSquare className="h-5 w-5" />, label: "Messages" },
    { icon: <Settings className="h-5 w-5" />, label: "Settings" },
];

export default function StudentDashboard() {
    const [mounted, setMounted] = useState(false);
    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [active, setActive] = useState("Overview");
    const [query, setQuery] = useState("");

    useEffect(() => {
        console.log("🎓 StudentDashboard: Mounted");
        setMounted(true);
        console.log("🔧 Initializing student data layer...");
        return () => {
            console.log("🛑 StudentDashboard: Unmounted");
            setMounted(false);
        };
    }, []);

    // keyboard: toggle sidebar with `[` and focus search with "/"
    useEffect(() => {
        const handler = (e: KeyboardEvent) => {
            if (e.key === "[") setSidebarOpen((s) => !s);
            if (e.key === "/") {
                e.preventDefault();
                const el = document.getElementById("global-search");
                if (el) (el as HTMLInputElement).focus();
            }
        };
        window.addEventListener("keydown", handler);
        return () => window.removeEventListener("keydown", handler);
    }, []);

    const filtered = useMemo(
        () =>
            navItems.filter((n) =>
                n.label.toLowerCase().includes(query.toLowerCase())
            ),
        [query]
    );

    return (
        <div className="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-50">
            {/* Top Toolbar */}
            <header className="sticky top-0 z-40 border-b border-gray-200/70 dark:border-gray-800/80 backdrop-blur bg-white/75 dark:bg-gray-950/60">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex h-14 items-center justify-between">
                        <div className="flex items-center gap-2">
                            <IconButton
                                title="Toggle sidebar"
                                onClick={() => setSidebarOpen((s) => !s)}
                            >
                                {sidebarOpen ? (
                                    <X className="h-5 w-5" />
                                ) : (
                                    <Menu className="h-5 w-5" />
                                )}
                            </IconButton>
                            <div className="text-sm font-medium text-gray-500">
                                Student
                            </div>
                            <div className="text-sm">/</div>
                            <h1 className="text-sm font-semibold">{active}</h1>
                        </div>

                        <div className="flex items-center">
                            <div className="relative hidden sm:block">
                                <Search className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input
                                    id="global-search"
                                    value={query}
                                    onChange={(e) => setQuery(e.target.value)}
                                    placeholder="Search… (/ to focus)"
                                    className="w-64 rounded-2xl border border-gray-200 bg-white pl-9 pr-3 py-2 text-sm shadow-sm outline-none focus:ring-2 focus:ring-blue-500/40 dark:bg-gray-900 dark:border-gray-800"
                                />
                            </div>

                            <ToolbarDivider />
                            <IconButton title="Notifications">
                                <Bell className="h-5 w-5" />
                            </IconButton>
                            <ToolbarDivider />
                            <button className="rounded-2xl bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700 active:scale-[.99]">
                                New Note
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-12 gap-4 py-4">
                    {/* Sidebar */}
                    <aside
                        className={`${
                            sidebarOpen
                                ? "col-span-12 md:col-span-3"
                                : "hidden md:block md:col-span-2"
                        }`}
                    >
                        <div className="rounded-2xl border border-gray-200 bg-white p-2 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                            <nav className="space-y-1">
                                {(query ? filtered : navItems).map((item) => (
                                    <button
                                        key={item.label}
                                        onClick={() => setActive(item.label)}
                                        className={`w-full flex items-center gap-3 rounded-xl px-3 py-2 text-sm transition hover:bg-gray-100 dark:hover:bg-gray-800 ${
                                            active === item.label
                                                ? "bg-gray-100 dark:bg-gray-800 font-semibold"
                                                : ""
                                        }`}
                                        aria-current={
                                            active === item.label
                                                ? "page"
                                                : undefined
                                        }
                                    >
                                        {item.icon}
                                        <span>{item.label}</span>
                                    </button>
                                ))}
                            </nav>

                            <div className="mt-3 border-t border-gray-200 pt-3 dark:border-gray-800">
                                <button className="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-xs text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                                    <LogOut className="h-4 w-4" /> Sign out
                                </button>
                            </div>
                        </div>
                    </aside>

                    {/* Content */}
                    <main
                        className={`${
                            sidebarOpen
                                ? "col-span-12 md:col-span-9"
                                : "col-span-12 md:col-span-10"
                        }`}
                    >
                        <div className="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                            <div className="mb-4 flex flex-wrap items-center justify-between gap-2">
                                <div className="text-lg font-semibold">
                                    {active}
                                </div>
                                <div className="flex items-center gap-2">
                                    <button className="rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-sm hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:hover:bg-gray-800">
                                        Filter
                                    </button>
                                    <button className="rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-sm hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:hover:bg-gray-800">
                                        Export
                                    </button>
                                </div>
                            </div>

                            <div className="grid gap-3 md:grid-cols-3">
                                <div className="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                                    <div className="text-sm text-gray-500">
                                        Data Layer
                                    </div>
                                    <div className="text-2xl font-semibold">
                                        {mounted ? "Mounted" : "Not Ready"}
                                    </div>
                                </div>
                                <div className="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                                    <div className="text-sm text-gray-500">
                                        Active Section
                                    </div>
                                    <div className="text-2xl font-semibold">
                                        {active}
                                    </div>
                                </div>
                                <div className="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                                    <div className="text-sm text-gray-500">
                                        Search
                                    </div>
                                    <div className="truncate text-2xl font-semibold">
                                        {query || "—"}
                                    </div>
                                </div>
                            </div>

                            <div className="mt-4 rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700">
                                This is your main content area. Swap this with
                                tables, charts, or lesson views.
                            </div>
                        </div>
                    </main>
                </div>
            </div>

            {/* Footer */}
            <footer className="mx-auto max-w-7xl px-4 pb-6 pt-2 text-center text-xs text-gray-500 sm:px-6 lg:px-8">
                Student Dashboard • Sidebar ( [ ] ) • Focus Search ( / )
            </footer>
        </div>
    );
}
