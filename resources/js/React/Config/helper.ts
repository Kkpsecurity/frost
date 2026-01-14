export function defaultAvatar(name: string): string {
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(
        name
    )}&size=32&background=6c757d&color=ffffff&rounded=true`;
}