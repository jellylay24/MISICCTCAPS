<script>
(function() {
    var latestId = {{ $latestId ?? 0 }};
    var role = '{{ $role ?? 'faculty' }}';

    function checkUpdates() {
        var url = '/borrow-updates?since=' + latestId + '&role=' + role;
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(data) {
            if (data.count > 0 && data.max_id > latestId) {
                latestId = data.max_id;
                // Highlight the table briefly to signal new data
                var table = document.querySelector('.bg-white.rounded-2xl.overflow-hidden');
                if (table) {
                    table.style.transition = 'box-shadow 0.5s';
                    table.style.boxShadow = '0 0 0 2px #1a237e';
                    setTimeout(function() {
                        table.style.boxShadow = '';
                    }, 2000);
                }
            }
        })
        .catch(function(err) {
            // Silent fail
        });
    }

    // Check every 5 seconds
    setInterval(checkUpdates, 5000);
})();
</script>
