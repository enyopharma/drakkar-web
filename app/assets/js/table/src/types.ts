export type ProteinType = 'h' | 'v'

export type Description = {
    id: number
    pmid: number
    run_id: number
    stable_id: string
    version: number
    obsolete: boolean
    method: {
        id: number
        psimi_id: string
    }
    interactor1: Interactor
    interactor2: Interactor
    created_at: string
    deleted_at: string
    deleted: boolean
}

export type Interactor = {
    protein: {
        id: number
        accession: string
    }
    name: string
    start: number
    stop: number
    mapping: Alignment[]
}

export type Alignment = {
    sequence: string
    isoforms: Isoform[]
}

export type Isoform = {
    accession: string
    occurrences: Array<{ start: number, stop: number }>
}

export type Protein = {
    type: ProteinType
    accession: string
    sequence: string
    isoforms: Array<{ accession: string, sequence: string }>,
}
