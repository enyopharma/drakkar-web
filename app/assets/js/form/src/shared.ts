
export const extract = (sequence: string, subsequence: string): [number, number] => {
    const source = sequence.toLowerCase()
    const target = subsequence.toLowerCase()
    const start = source.indexOf(target)
    const stop = start + target.length

    return start >= 0 && subsequence.trim().length > 0
        ? [start + 1, stop]
        : [-1, -1]
}
