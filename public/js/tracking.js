(function () {

```
const form =
    document.getElementById('searchForm');

const input =
    document.getElementById('searchInput');

const clearBtn =
    document.getElementById('clearBtn');

if (!form || !input) {
    return;
}

let debounceTimer;

input.addEventListener('input', function () {

    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(function () {

        form.submit();

    }, 500);

});

if (clearBtn) {

    clearBtn.addEventListener(
        'click',
        function () {

            input.value = '';

            form.submit();

        }
    );

}
```

})();
