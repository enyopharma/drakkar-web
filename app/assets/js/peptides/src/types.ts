export type PeptideType = 'h' | 'v'

export type Peptide = {
    description_id: number
    stable_id: number
    type: PeptideType
    sequence: string
    data: {
        hotspots: Hotspots
    }
}

export type Hotspots = Record<number, string>
