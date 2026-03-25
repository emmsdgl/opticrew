========================================
  ALGORITHM COMPARISON - LOCAL TESTING
========================================

This folder is for THESIS DEFENSE ONLY.
DELETE this entire folder after defense.

Access: http://localhost/opticrew/algorithm-comparison/

What it does:
- Compares Rule-Based + Genetic Algorithm (Hybrid) vs Traditional GA
- Uses real data from the opticrew database
- Generates comparison tables matching the thesis document (Tables 16-18)

Files:
  index.php                        - Main UI page
  algorithms/HybridGA.php          - Rule-Based + GA with Elitism
  algorithms/TraditionalGA.php     - Traditional GA (no preprocessing)
  algorithms/RuleBasedPreprocessor.php - Rule-based filtering/team formation

To delete: Just remove this entire "algorithm-comparison" folder.
No other files in the project depend on it.
