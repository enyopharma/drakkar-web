const formatters = {
    source: ({ protein }) => {
        return {
            type: protein == null ? '' : protein.type,
            sequence: protein == null ? '' : protein.sequence,
            matures: protein == null ? [] : protein.matures,
            chains: protein == null ? [] : protein.chains,
        }
    },

    protein: (data) => {
        if (data.start == '' || data.stop == '' || data.protein == null) {
            return {
                type: '',
                name: '',
                start: '',
                stop: '',
                sequence: '',
                sequences: [],
                domains: [],
                mapping: [],
            }
        }

        const sequence = data.protein.sequence.slice(data.start - 1, data.stop)

        const canonical = { [data.protein.accession]: sequence }

        const sequences = data.start == 1 && data.stop == data.protein.sequence.length
            ? Object.assign({}, canonical, data.protein.isoforms)
            : canonical

        return {
            type: data.protein.type,
            name: data.name,
            start: data.start,
            stop: data.stop,
            sequence: sequence,
            sequences: sequences,
            domains: data.protein.domains.map(domain => {
                return {
                    key: domain.key,
                    description: domain.description,
                    start: domain.start - data.start + 1,
                    stop: domain.stop - data.start + 1,
                    valid: domain.start >= data.start && domain.stop <= data.stop,
                }
            }),
            mapping: data.mapping.map(alignment => {
                return formatters.alignment(alignment, sequences)
            })
        }
    },

    alignment: (alignment, sequences) => {
        if (alignment == null) return {}

        // inject the sequence of the isoforms.
        return Object.assign(alignment, {
            isoforms: alignment.isoforms.map(isoform => {
                return Object.assign(isoform, {
                    sequence: sequences[isoform.accession]
                })
            })
        })
    },
}

export default formatters
