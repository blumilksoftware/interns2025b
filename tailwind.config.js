module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                  light: '#025F60',
                  DEFAULT: '#024C4D',
                  dark: '#024445',
                },
                shadow:
                {
                  DEFAULT: '#000000',
                  blue: '#375DFB',
                },
            },
        },
    },
    plugins: [],
}
