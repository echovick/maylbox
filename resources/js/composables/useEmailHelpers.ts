import type { EmailAddress } from '@/types/email';

export function useEmailHelpers() {
    /**
     * Format date to relative time (e.g., "2 minutes ago", "Yesterday")
     */
    function formatRelativeDate(dateString: string): string {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now.getTime() - date.getTime();
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);

        if (diffSec < 60) {
            return 'Just now';
        } else if (diffMin < 60) {
            return `${diffMin} minute${diffMin > 1 ? 's' : ''} ago`;
        } else if (diffHour < 24) {
            return `${diffHour} hour${diffHour > 1 ? 's' : ''} ago`;
        } else if (diffDay === 1) {
            return 'Yesterday';
        } else if (diffDay < 7) {
            return `${diffDay} days ago`;
        } else {
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined,
            });
        }
    }

    /**
     * Format date for email header (e.g., "Dec 12, 2025, 4:30 PM")
     */
    function formatEmailDate(dateString: string): string {
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
        });
    }

    /**
     * Format file size (e.g., "1.2 MB", "345 KB")
     */
    function formatFileSize(bytes: number): string {
        if (bytes === 0) return '0 B';

        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
    }

    /**
     * Get initials from name or email
     */
    function getInitials(address: EmailAddress): string {
        if (address.name) {
            const parts = address.name.trim().split(' ');
            if (parts.length >= 2) {
                return `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase();
            }
            return address.name.substring(0, 2).toUpperCase();
        }

        // Use email if no name
        return address.email.substring(0, 2).toUpperCase();
    }

    /**
     * Get color for avatar based on email
     */
    function getAvatarColor(email: string): string {
        // Simple hash to generate consistent color
        let hash = 0;
        for (let i = 0; i < email.length; i++) {
            hash = email.charCodeAt(i) + ((hash << 5) - hash);
        }

        const colors = [
            'bg-blue-500',
            'bg-green-500',
            'bg-purple-500',
            'bg-pink-500',
            'bg-indigo-500',
            'bg-orange-500',
            'bg-teal-500',
            'bg-cyan-500',
        ];

        return colors[Math.abs(hash) % colors.length];
    }

    /**
     * Truncate text to specified length
     */
    function truncate(text: string, length: number): string {
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }

    /**
     * Format email address for display
     */
    function formatEmailAddress(address: EmailAddress): string {
        if (address.name) {
            return `${address.name} <${address.email}>`;
        }
        return address.email;
    }

    /**
     * Format multiple recipients
     */
    function formatRecipients(recipients: EmailAddress[], limit = 3): string {
        if (recipients.length === 0) return '';

        const formatted = recipients
            .slice(0, limit)
            .map(addr => addr.name || addr.email)
            .join(', ');

        if (recipients.length > limit) {
            return `${formatted}, +${recipients.length - limit} more`;
        }

        return formatted;
    }

    /**
     * Get file icon based on content type
     */
    function getFileIcon(contentType: string): string {
        if (contentType.startsWith('image/')) return 'image';
        if (contentType.startsWith('video/')) return 'video';
        if (contentType.startsWith('audio/')) return 'music';
        if (contentType.includes('pdf')) return 'file-text';
        if (contentType.includes('word')) return 'file-text';
        if (contentType.includes('excel') || contentType.includes('spreadsheet'))
            return 'file-spreadsheet';
        if (contentType.includes('presentation')) return 'presentation';
        if (contentType.includes('zip') || contentType.includes('compressed'))
            return 'archive';
        return 'file';
    }

    return {
        formatRelativeDate,
        formatEmailDate,
        formatFileSize,
        getInitials,
        getAvatarColor,
        truncate,
        formatEmailAddress,
        formatRecipients,
        getFileIcon,
    };
}
