export type Run = {
    id: number
}

export type Publication = {
    pmid: number
}

export type Peptide = {
    description_id: number
    stable_id: number
    type: 'h' | 'v'
    sequence: string
    data: PeptideData
}

export type PeptideData = {
    cter: string
    nter: string
    affinity: {
        type: string
        value: number | null
        unit: string
    }
    hotspots: Hotspots
    methods: {
        expression: string
        interaction: string
    }
    info: string
}

export type Hotspots = Record<number, string>
