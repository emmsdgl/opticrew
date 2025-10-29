/**
 * Geofencing Component for Clock In/Out
 * Checks user's location and enables/disables clock in/out based on distance from office
 */

// Prevent redeclaration if script is loaded multiple times
if (typeof window.Geofencing === 'undefined') {

class Geofencing {
    constructor(config) {
        // Set button and status IDs first (needed for showNoTaskError)
        this.buttonId = config.buttonId;
        this.statusElementId = config.statusElementId;
        this.distanceElementId = config.distanceElementId;

        // Handle case where no task location is available
        if (!config.officeLatitude || !config.officeLongitude) {
            this.noTaskLocation = true;
            this.locationName = config.locationName || 'No task location';
            this.showNoTaskError(config.message || 'No task assigned for today');
            return;
        }

        this.officeLocation = {
            lat: parseFloat(config.officeLatitude),
            lng: parseFloat(config.officeLongitude)
        };
        this.radius = parseFloat(config.radius); // in meters
        this.locationName = config.locationName || 'Task location';
        this.locationType = config.locationType || 'task';

        this.userLocation = null;
        this.distance = null;
        this.isInRange = false;
        this.watchId = null;
        this.noTaskLocation = false;

        this.init();
    }

    init() {
        if (!navigator.geolocation) {
            this.showError('Geolocation is not supported by your browser');
            return;
        }

        // Start watching user's position
        this.startWatching();
    }

    showNoTaskError(message) {
        const button = document.getElementById(this.buttonId);
        const statusElement = document.getElementById(this.statusElementId);

        if (button) {
            button.classList.remove('bg-[#2A6DFA]', 'hover:bg-[#2558d6]');
            button.classList.add('bg-gray-300', 'dark:bg-gray-600', 'cursor-not-allowed', 'pointer-events-none');
            button.disabled = true;
        }

        if (statusElement) {
            statusElement.innerHTML = `<i class="fa-solid fa-calendar-xmark text-orange-500"></i> <span class="text-orange-600 dark:text-orange-400">${message}</span>`;
        }
    }

    startWatching() {
        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 30000 // Cache position for 30 seconds
        };

        this.watchId = navigator.geolocation.watchPosition(
            (position) => this.onLocationSuccess(position),
            (error) => this.onLocationError(error),
            options
        );
    }

    stopWatching() {
        if (this.watchId) {
            navigator.geolocation.clearWatch(this.watchId);
        }
    }

    onLocationSuccess(position) {
        this.userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        // Calculate distance from office
        this.distance = this.calculateDistance(
            this.userLocation.lat,
            this.userLocation.lng,
            this.officeLocation.lat,
            this.officeLocation.lng
        );

        this.isInRange = this.distance <= this.radius;

        // Update UI
        this.updateButton();
        this.updateStatus();
        this.updateDistance();
    }

    onLocationError(error) {
        let errorMessage = '';

        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'Location permission denied. Please enable location access.';
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'Location information unavailable.';
                break;
            case error.TIMEOUT:
                errorMessage = 'Location request timed out.';
                break;
            default:
                errorMessage = 'An unknown error occurred.';
        }

        this.showError(errorMessage);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in meters
     */
    calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Earth's radius in meters
        const dLat = this.toRadians(lat2 - lat1);
        const dLon = this.toRadians(lon2 - lon1);

        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(this.toRadians(lat1)) * Math.cos(this.toRadians(lat2)) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distance = R * c;

        return Math.round(distance); // Return rounded distance in meters
    }

    toRadians(degrees) {
        return degrees * (Math.PI / 180);
    }

    updateButton() {
        const button = document.getElementById(this.buttonId);
        if (!button) return;

        if (this.isInRange) {
            // Enable button - blue background
            button.classList.remove('bg-gray-300', 'dark:bg-gray-600', 'cursor-not-allowed', 'pointer-events-none');
            button.classList.add('bg-[#2A6DFA]', 'hover:bg-[#2558d6]', 'cursor-pointer');
            button.disabled = false;
        } else {
            // Disable button - gray background
            button.classList.remove('bg-[#2A6DFA]', 'hover:bg-[#2558d6]', 'cursor-pointer');
            button.classList.add('bg-gray-300', 'dark:bg-gray-600', 'cursor-not-allowed', 'pointer-events-none');
            button.disabled = true;
        }
    }

    updateStatus() {
        const statusElement = document.getElementById(this.statusElementId);
        if (!statusElement) return;

        if (this.isInRange) {
            statusElement.innerHTML = '<i class="fa-solid fa-check-circle text-green-500"></i> <span class="text-green-600 dark:text-green-400">In Range - Clock In/Out Available</span>';
        } else {
            statusElement.innerHTML = '<i class="fa-solid fa-location-crosshairs text-red-500"></i> <span class="text-red-600 dark:text-red-400">Out of Range - Move closer to task location</span>';
        }
    }

    updateDistance() {
        const distanceElement = document.getElementById(this.distanceElementId);
        if (!distanceElement) return;

        if (this.distance !== null) {
            const distanceText = this.distance < 1000
                ? `${this.distance}m from task location`
                : `${(this.distance / 1000).toFixed(2)}km from task location`;

            distanceElement.textContent = distanceText;
            distanceElement.classList.remove('hidden');
        }
    }

    showError(message) {
        const button = document.getElementById(this.buttonId);
        const statusElement = document.getElementById(this.statusElementId);

        if (button) {
            button.classList.remove('bg-[#2A6DFA]', 'hover:bg-[#2558d6]');
            button.classList.add('bg-gray-300', 'dark:bg-gray-600', 'cursor-not-allowed', 'pointer-events-none');
            button.disabled = true;
        }

        if (statusElement) {
            statusElement.innerHTML = `<i class="fa-solid fa-exclamation-triangle text-yellow-500"></i> <span class="text-yellow-600 dark:text-yellow-400">${message}</span>`;
        }
    }

    getUserLocation() {
        return this.userLocation;
    }

    getDistance() {
        return this.distance;
    }

    isUserInRange() {
        return this.isInRange;
    }
}

// Make it globally accessible
window.Geofencing = Geofencing;

} // End of redeclaration guard
