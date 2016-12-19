//uraditi grupisanje prema opstini


_
.chain(temp1)
.groupBy('opstina')
.map(function(value, key) {
    return {
        opstina: key,
        podaci: value
    }
})
.value();