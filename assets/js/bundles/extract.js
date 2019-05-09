export default function (subsequence, sequence) {
    const source = sequence.toLowerCase()
    const target = subsequence.trim().toLowerCase()
    const start = source.indexOf(target) + 1
    const stop = start + target.length - 1

    return [start, stop]
}
