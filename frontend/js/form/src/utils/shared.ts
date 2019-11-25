
export const extract = (sequence: string, subsequence: string): [number, number] => {
    const source = sequence.toLowerCase()
    const target = subsequence.toLowerCase()
    const start = source.indexOf(target) + 1
    const stop = start + target.length - 1

    return [start, stop]
}
