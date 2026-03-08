from itertools import permutations
import time

def solve_cryptarithmetic():
    # Letters involved: T, W, O, U, V, P, A, I, R, S
    # There are 10 distinct letters: T, W, O, U, V, P, A, I, R, S
    letters = ('T', 'W', 'O', 'U', 'V', 'P', 'A', 'I', 'R', 'S')

    # We need to find a permutation of 0-9 for these 10 letters
    digits = range(10)


    for p in permutations(digits):
        d = dict(zip(letters, p))

        # Leading digit checks
        if d['U'] == 0 or d['V'] == 0 or d['R'] == 0 or d['S'] == 0:
            continue

        # UV + UV + V = VAR
        # (10*U + V) + (10*U + V) + V = 100*V + 10*A + R
        # 20*U + 3*V = 100*V + 10*A + R
        if 20 * d['U'] + 3 * d['V'] != 100 * d['V'] + 10 * d['A'] + d['R']:
            continue

        # R * P * P = AIR
        # R * P^2 = 100*A + 10*I + R
        if d['R'] * (d['P']**2) != 100 * d['A'] + 10 * d['I'] + d['R']:
            continue

        # SO + SO = VOW
        # (10*S + O) + (10*S + O) = 100*V + 10*O + W
        # 20*S + 2*O = 100*V + 10*O + W
        if 20 * d['S'] + 2 * d['O'] != 100 * d['V'] + 10 * d['O'] + d['W']:
            continue

        return d

result = solve_cryptarithmetic()

print(result)

# {'T': 8, 'W': 0, 'O': 5, 'U': 6, 'V': 1, 'P': 9, 'A': 2, 'I': 4, 'R': 3, 'S': 7}